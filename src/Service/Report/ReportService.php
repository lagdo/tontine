<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Meeting\PoolService;
use Siak\Tontine\Service\Meeting\ReportService as MeetingReportService;
use Siak\Tontine\Service\Planning\SubscriptionService;

class ReportService implements ReportServiceInterface
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
     * @var PoolService
     */
    private $poolService;

    /**
     * @var FeeService
     */
    private $feeService;

    /**
     * @var FineService
     */
    private $fineService;

    /**
     * @var MeetingReportService
     */
    private $meetingReportService;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

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
     * @param PoolService $poolService
     * @param FeeService $feeService
     * @param FineService $fineService
     * @param MeetingReportService $meetingReportService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        PoolService $poolService, FeeService $feeService, FineService $fineService,
        MeetingReportService $meetingReportService, SubscriptionService $subscriptionService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->poolService = $poolService;
        $this->feeService = $feeService;
        $this->fineService = $fineService;
        $this->meetingReportService = $meetingReportService;
        $this->subscriptionService = $subscriptionService;
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
            $amount = $pool->deposits_count * $pool->amount;
            $this->depositsAmount += $amount;
            $pool->deposits_amount = $this->localeService->formatMoney($amount);

            // Payables
            $query = $session->payables()->whereIn('subscription_id', $subscriptionIds);
            // Expected
            $pool->payables_count = $this->meetingReportService->getSessionRemitmentCount($pool, $session);
            // Paid
            $pool->remitments_count = $query->whereHas('remitment')->count();
            // Amount
            $amount = $pool->remitments_count * $pool->amount * $subscriptionCount;
            $this->remitmentsAmount += $amount;
            $pool->remitments_amount = $this->localeService->formatMoney($amount);
        });
    }

    /**
     * @param integer $sessionId
     *
     * @return array
     */
    public function getSessionReport(int $sessionId): array
    {
        $tontine = $this->tenantService->tontine();
        $session = $this->tenantService->getSession($sessionId);
        [$countries] = $this->localeService->getNamesFromTontine($tontine);

        $pools = $this->getPools($session);

        $charges = $this->tenantService->tontine()->charges;
        // Fees
        [$bills, $settlements] = $this->feeService->getBills($session);
        $fees = [
            'bills' => $bills,
            'settlements' => $settlements,
            'zero' => $settlements['zero'],
            'fees' => $charges->filter(function($charge) use($bills, $settlements) {
                return $charge->is_fee &&
                    (isset($bills['total']['current'][$charge->id]) ||
                    isset($settlements['total']['current'][$charge->id]));
            })
        ];
        // Fines
        [$bills, $settlements] = $this->fineService->getBills($session);
        $fines = [
            'bills' => $bills,
            'settlements' => $settlements,
            'zero' => $settlements['zero'],
            'fines' => $charges->filter(function($charge) use($bills, $settlements) {
                return $charge->is_fine &&
                    (isset($bills['total']['current'][$charge->id]) ||
                    isset($settlements['total']['current'][$charge->id]));
            })
        ];

        return [
            'tontine' => $tontine,
            'session' => $session,
            'countries' => $countries,
            'deposits' => [
                'session' => $session,
                'pools' => $pools,
                'amount' => $this->localeService->formatMoney($this->depositsAmount),
            ],
            'remitments' => [
                'session' => $session,
                'pools' => $pools,
                'amount' => $this->localeService->formatMoney($this->remitmentsAmount),
            ],
            'fees' => $fees,
            'fines' => $fines,
        ];

        /*if($tontine->is_financial)
        {
            [$loans, $sum] = $loanService->getSessionLoans($session);
            $amountAvailable = $loanService->getAmountAvailable($session);
            $html->with('loans', [
                'session' => $session,
                'loans' => $loans,
                'sum' => $sum,
                'amountAvailable' => $this->localeService->formatMoney($amountAvailable),
            ]);
            $html->with('refunds', [
                'session' => $session,
                'loans' => $refundService->getLoans($session, true),
                'refundSum' => $refundService->getRefundSum($session),
            ]);
        }*/
    }

    /**
     * @param integer $poolId
     *
     * @return array
     */
    public function getPoolReport(int $poolId): array
    {
        $pool = $this->subscriptionService->getPool($poolId);
        $tontine = $this->meetingReportService->getTontine();
        [$countries, $currencies] = $this->localeService->getNamesFromTontine($tontine);

        return [
            'figures' => $this->meetingReportService->getFigures($pool),
            'tontine' => $tontine,
            'countries' => $countries,
            'currencies' => $currencies,
            'pool' => $pool,
            'pools' > $this->subscriptionService->getPools(),
        ];
    }
}
