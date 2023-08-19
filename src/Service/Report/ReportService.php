<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Report\RoundService;

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
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        SessionService $sessionService, MemberService $memberService, RoundService $roundService,
        SubscriptionService $subscriptionService, SummaryService $summaryService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->sessionService = $sessionService;
        $this->memberService = $memberService;
        $this->subscriptionService = $subscriptionService;
        $this->roundService = $roundService;
        $this->summaryService = $summaryService;
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
                'loans' => $this->memberService->getAuctions($session),
                // 'total' => $this->sessionService->getAuction($session),
            ],
            'fees' => [
                'bills' => $this->memberService->getFees($session),
                'fees' => $this->sessionService->getFees($session),
            ],
            'fines' => [
                'bills' => $this->memberService->getFines($session),
                'fines' => $this->sessionService->getFines($session),
            ],
            'loans' => [
                'loans' => $this->memberService->getLoans($session),
                'total' => $this->sessionService->getLoan($session),
            ],
            'refunds' => [
                'debts' => $this->memberService->getDebts($session),
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

        $sessions = $round->sessions;
        // Sessions with data
        $sessionIds = $sessions->filter(function($session) {
            return $session->status === Session::STATUS_CLOSED ||
                $session->status === Session::STATUS_OPENED;
        })->pluck('id');
        $amounts = [
            'sessions' => $sessions,
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
