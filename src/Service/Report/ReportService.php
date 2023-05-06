<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Charge\FeeService;
use Siak\Tontine\Service\Meeting\Charge\FineService;
use Siak\Tontine\Service\Meeting\SummaryService;
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
     * @var FeeService
     */
    private FeeService $feeService;

    /**
     * @var FineService
     */
    private FineService $fineService;

    /**
     * @var SubscriptionService
     */
    private SubscriptionService $subscriptionService;

    /**
     * @var SummaryService
     */
    private SummaryService $summaryService;

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
     * @param FeeService $feeService
     * @param FineService $fineService
     * @param SubscriptionService $subscriptionService
     * @param SummaryService $summaryService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService, FeeService $feeService,
        FineService $fineService, SubscriptionService $subscriptionService, SummaryService $summaryService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->feeService = $feeService;
        $this->fineService = $fineService;
        $this->subscriptionService = $subscriptionService;
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
            $amount = $pool->deposits_count * $pool->amount;
            $this->depositsAmount += $amount;
            $pool->deposits_amount = $this->localeService->formatMoney($amount);

            // Payables
            $query = $session->payables()->whereIn('subscription_id', $subscriptionIds);
            // Expected
            $pool->payables_count = $this->summaryService->getSessionRemitmentCount($pool, $session);
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
        [$country] = $this->localeService->getNameFromTontine($tontine);

        $pools = $this->getPools($session);

        $charges = $this->tenantService->tontine()->charges;
        // Fees
        $bills = $this->feeService->getBills($session);
        $settlements = $this->feeService->getSettlements($session);
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
        $bills = $this->fineService->getBills($session);
        $settlements = $this->fineService->getSettlements($session);
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
            'country' => $country,
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
        $report = $this->summaryService->getFigures($pool);
        $tontine = $this->tenantService->tontine();
        [$country, $currency] = $this->localeService->getNameFromTontine($tontine);

        $report['tontine'] = $tontine;
        $report['country'] = $country;
        $report['currency'] = $currency;

        return $report;
    }
}
