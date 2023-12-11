<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Cash\SavingService;
use Siak\Tontine\Service\Meeting\Credit\ProfitService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Report\RoundService;
use Siak\Tontine\Service\Tontine\FundService;

use function compact;
use function strtolower;
use function trans;

class ReportService
{
    /**
     * @var int
     */
    private $depositsAmount;

    /**
     * @var int
     */
    private $remitmentsAmount;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param MemberService $memberService
     * @param RoundService $roundService
     * @param FundService $fundService
     * @param SavingService $savingService
     * @param SummaryService $summaryService
     * @param ReportService $reportService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService,
        protected MemberService $memberService, protected RoundService $roundService,
        protected FundService $fundService, protected SavingService $savingService,
        protected SummaryService $summaryService, protected ProfitService $profitService,
        protected SubscriptionService $subscriptionService)
    {}

    /**
     * Get pools with receivables and payables.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getPools(Session $session): Collection
    {
        $this->depositsAmount = 0;
        $this->remitmentsAmount = 0;
        $pools = $this->tenantService->round()->pools;

        return $pools->each(function($pool) use($session) {
            $subscriptionCount = $pool->subscriptions()->count();
            $subscriptionIds = $pool->subscriptions()->pluck('id');
            // Receivables
            $query = $session->receivables()->whereIn('subscription_id', $subscriptionIds);
            // Expected
            $pool->receivables_count = $query->count();
            // Paid
            $pool->deposits_count = $query->whereHas('deposit')->count();
            // Amount
            $pool->deposits_amount = $pool->deposits_count * $pool->amount;
            $this->depositsAmount += $pool->deposits_amount;

            // Payables
            $query = $session->payables()->whereIn('subscription_id', $subscriptionIds);
            // Expected
            $pool->payables_count = $this->summaryService->getSessionRemitmentCount($pool, $session);
            // Paid
            $pool->remitments_count = $query->whereHas('remitment')->count();
            // Amount
            $pool->remitments_amount = $pool->remitments_count * $pool->amount * $subscriptionCount;
            $this->remitmentsAmount += $pool->remitments_amount;
        });
    }

    /**
     * @param Session $session
     * @param Collection $funds
     *
     * @return array
     */
    private function getFundClosings(Session $session, Collection $funds): array
    {
        $closings = $this->savingService->getSessionClosings($session);
        return Arr::only($closings, $funds->keys()->all());
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSessionReport(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        [$country] = $this->localeService->getNameFromTontine($tontine);
        $funds = $this->fundService->getFundList();
        $closings = $this->getFundClosings($session, $funds);
        $profits = Arr::map($closings, fn($amount, $fundId) => [
            'fund' => $funds[$fundId],
            'profitAmount' => $amount,
            'savings' => $this->profitService->getDistributions($session, $fundId, $amount),
        ]);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'country' => $country,
            'deposits' => [
                'session' => $session,
                'receivables' => $this->memberService->getReceivables($session),
                'pools' => $this->sessionService->getReceivables($session),
            ],
            'remitments' => [
                'session' => $session,
                'payables' => $this->memberService->getPayables($session),
                'pools' => $this->sessionService->getPayables($session),
                'auctions' => $this->memberService->getAuctions($session),
            ],
            'bills' => [
                'bills' => $this->memberService->getBills($session),
                'charges' => [
                    'session' => $this->sessionService->getSessionCharges($session),
                    'total' => $this->sessionService->getTotalCharges($session),
                ],
            ],
            'loans' => [
                'loans' => $this->memberService->getLoans($session),
                'total' => $this->sessionService->getLoan($session),
            ],
            'refunds' => [
                'refunds' => $this->memberService->getRefunds($session),
                'total' => $this->sessionService->getRefund($session),
            ],
            'savings' => [
                'savings' => $this->memberService->getSavings($session),
                'funds' => $funds,
                'total' => $this->sessionService->getSaving($session),
            ],
            'disbursements' => [
                'disbursements' => $this->memberService->getDisbursements($session),
                'total' => $this->sessionService->getDisbursement($session),
            ],
            'profits' => $profits,
        ];
    }

    /**
     * @param Round $round
     *
     * @return array
     */
    public function getRoundReport(Round $round): array
    {
        $tontine = $this->tenantService->tontine();
        [$country, $currency] = $this->localeService->getNameFromTontine($tontine);

        $pools = $this->subscriptionService->getPools(false)
            ->each(function($pool) {
                $pool->figures = $this->summaryService->getFigures($pool);
            });

        $sessions = $round->sessions()->orderBy('start_at', 'asc')->get();
        // Sessions with data
        $sessionIds = $sessions->filter(function($session) {
            return $session->status === Session::STATUS_CLOSED ||
                $session->status === Session::STATUS_OPENED;
        })->pluck('id');
        $amounts = [
            'sessions' => $sessions,
            'auctions' => $this->roundService->getAuctionAmounts($sessionIds),
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ];

        return compact('tontine', 'round', 'country', 'currency', 'pools', 'amounts');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function getSessionReportFilename(Session $session): string
    {
        return strtolower(trans('meeting.titles.report')) . '-' . Str::slug($session->title) . '.pdf';
    }

    /**
     * @param Round $round
     *
     * @return string
     */
    public function getRoundReportFilename(Round $round): string
    {
        return strtolower(trans('meeting.titles.report')) . '-' . Str::slug($round->title) . '.pdf';
    }
}
