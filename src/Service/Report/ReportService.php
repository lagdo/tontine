<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Credit\ProfitService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Report\RoundService;

use function compact;
use function strtolower;
use function trans;

class ReportService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var SessionService
     */
    private SessionService $sessionService;

    /**
     * @var MemberService
     */
    private MemberService $memberService;

    /**
     * @var SubscriptionService
     */
    private SubscriptionService $subscriptionService;

    /**
     * @var SummaryService
     */
    private SummaryService $summaryService;

    /**
     * @var RoundService
     */
    protected RoundService $roundService;

    /**
     * @var ProfitService
     */
    protected ProfitService $profitService;

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
     * @param SubscriptionService $subscriptionService
     * @param SummaryService $summaryService
     * @param ReportService $reportService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        SessionService $sessionService, MemberService $memberService,
        RoundService $roundService, SubscriptionService $subscriptionService,
        SummaryService $summaryService, ProfitService $profitService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->sessionService = $sessionService;
        $this->memberService = $memberService;
        $this->subscriptionService = $subscriptionService;
        $this->roundService = $roundService;
        $this->summaryService = $summaryService;
        $this->profitService = $profitService;
    }

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
     *
     * @return array
     */
    public function getSessionReport(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        [$country] = $this->localeService->getNameFromTontine($tontine);
        $profitSessionId = $session->round->properties['profit']['session'] ?? 0;
        $profitAmount = $session->round->properties['profit']['amount'] ?? 0;

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
            'fundings' => [
                'fundings' => $this->memberService->getFundings($session),
                'total' => $this->sessionService->getFunding($session),
            ],
            'disbursements' => [
                'disbursements' => $this->memberService->getDisbursements($session),
                'total' => $this->sessionService->getDisbursement($session),
            ],
            'profits' => [
                'show' => $profitSessionId === $session->id,
                'fundings' => $profitSessionId !== $session->id ? null :
                    $this->profitService->getDistributions($session, $profitAmount),
                'profitAmount' => $profitAmount,
            ],
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
            'fundings' => $this->roundService->getFundingAmounts($sessionIds),
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
