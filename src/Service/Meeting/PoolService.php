<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Tontine\TenantService;

class PoolService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * @var DepositService
     */
    protected DepositService $depositService;

    /**
     * @var RemitmentService
     */
    protected RemitmentService $remitmentService;

    /**
     * @param TenantService $tenantService
     * @param ReportService $reportService
     * @param DepositService $depositService
     * @param RemitmentService $remitmentService
     */
    public function __construct(TenantService $tenantService, ReportService $reportService,
        DepositService $depositService, RemitmentService $remitmentService)
    {
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
        $this->depositService = $depositService;
        $this->remitmentService = $remitmentService;
    }

    /**
     * @return Tontine|null
     */
    public function getTontine(): ?Tontine
    {
        return $this->tenantService->tontine();
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a paginated list of pools with receivables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithReceivables(Session $session, int $page = 0): Collection
    {
        $pools = $this->tenantService->round()->pools();
        if($page > 0 )
        {
            $pools->take($this->tenantService->getLimit());
            $pools->skip($this->tenantService->getLimit() * ($page - 1));
        }

        return $pools->get()->each(function($pool) use($session) {
            // Receivables
            $receivables = $this->depositService->getReceivables($pool, $session);
            // Expected
            $pool->recv_count = $receivables->count();
            // Paid
            $pool->recv_paid = $receivables->filter(function($receivable) {
                return $receivable->deposit !== null;
            })->count();
        });
    }

    /**
     * Get a paginated list of pools with payables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithPayables(Session $session, int $page = 0): Collection
    {
        $pools = $this->tenantService->round()->pools();
        if($page > 0 )
        {
            $pools->take($this->tenantService->getLimit());
            $pools->skip($this->tenantService->getLimit() * ($page - 1));
        }

        $sessions = $this->tenantService->round()->sessions;

        return $pools->get()->each(function($pool) use($session, $sessions) {
            // Payables
            $payables = $this->remitmentService->getPayables($pool, $session);
            // Expected
            // $pool->pay_count = $payables->count();
            // Paid
            $pool->pay_paid = $payables->filter(function($payable) {
                return $payable->remitment !== null;
            })->count();

            // Remitments
            $sessions = $sessions->filter(function($_session) use($pool) {
                return $_session->enabled($pool);
            });
            $sessionCount = $sessions->count();
            $sessionRank = $sessions->filter(function($_session) use($session) {
                return $_session->start_at->lt($session->start_at);
            })->count();
            $subscriptionCount = $pool->subscriptions()->count();
            $pool->pay_count = $this->reportService->getRemitmentCount($sessionCount, $subscriptionCount, $sessionRank);
        });
    }

    /**
     * Get pools report for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getPoolsReport(Session $session): array
    {
        $pools = $this->tenantService->round()->pools->keyBy('id');
        $sessions = $this->tenantService->round()->sessions;

        $payableSum = 0;
        $payableAmounts = $session->payableAmounts()->get()
            ->each(function($payable) use($pools, $sessions, &$payableSum) {
                $pool = $pools[$payable->id];
                $count = $sessions->filter(function($session) use($pool) {
                    return $session->enabled($pool);
                })->count();
                $payableSum += $payable->amount * $count;
                $payable->amount = Currency::format($payable->amount * $count);
            })->pluck('amount', 'id');

        $receivableSum = 0;
        $receivableAmounts = $session->receivableAmounts()->get()
            ->each(function($receivable) use(&$receivableSum) {
                $receivableSum += $receivable->amount;
                $receivable->amount = Currency::format($receivable->amount);
            })->pluck('amount', 'id');

        return [
            'payables' => $payableAmounts,
            'receivables' => $receivableAmounts,
            'sum' => [
                'payables' => Currency::format($payableSum),
                'receivables' => Currency::format($receivableSum),
            ],
        ];
    }
}