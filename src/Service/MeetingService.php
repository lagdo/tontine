<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;

use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

class MeetingService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PlanningService
     */
    protected PlanningService $planningService;

    /**
     * @var DepositService
     */
    protected DepositService $depositService;

    /**
     * @var RemittanceService
     */
    protected RemittanceService $remittanceService;

    /**
     * @var FeeSettlementService
     */
    protected FeeSettlementService $feeService;

    /**
     * @var FineSettlementService
     */
    protected FineSettlementService $fineService;

    /**
     * @param TenantService $tenantService
     * @param PlanningService $planningService
     * @param DepositService $depositService
     * @param RemittanceService $remittanceService
     * @param FeeSettlementService $feeService
     * @param FineSettlementService $fineService
     */
    public function __construct(TenantService $tenantService, PlanningService $planningService,
        DepositService $depositService, RemittanceService $remittanceService,
        FeeSettlementService $feeService, FineSettlementService $fineService)
    {
        $this->tenantService = $tenantService;
        $this->planningService = $planningService;
        $this->depositService = $depositService;
        $this->remittanceService = $remittanceService;
        $this->feeService = $feeService;
        $this->fineService = $fineService;
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
            $payables = $this->remittanceService->getPayables($pool, $session);
            // Expected
            // $pool->pay_count = $payables->count();
            // Paid
            $pool->pay_paid = $payables->filter(function($payable) {
                return $payable->remittance !== null;
            })->count();

            // Remittances
            $sessions = $sessions->filter(function($_session) use($pool) {
                return $_session->enabled($pool);
            });
            $sessionCount = $sessions->count();
            $sessionRank = $sessions->filter(function($_session) use($session) {
                return $_session->start_at->lt($session->start_at);
            })->count();
            $subscriptionCount = $pool->subscriptions()->count();
            $pool->pay_count = $this->planningService->getRemittanceCount($sessionCount, $subscriptionCount, $sessionRank);
        });
    }

    /**
     * Update a session agenda.
     *
     * @param Session $session
     * @param string $agenda
     *
     * @return void
     */
    public function updateSessionAgenda(Session $session, string $agenda): void
    {
        $session->update(['agenda' => $agenda]);
    }

    /**
     * Update a session report.
     *
     * @param Session $session
     * @param string $report
     *
     * @return void
     */
    public function updateSessionReport(Session $session, string $report): void
    {
        $session->update(['report' => $report]);
    }

    /**
     * Find the unique receivable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     * @param string $notes
     *
     * @return int
     */
    public function saveReceivableNotes(Pool $pool, Session $session, int $receivableId, string $notes): int
    {
        return $session->receivables()->where('id', $receivableId)
            ->whereIn('subscription_id', $pool->subscriptions()->pluck('id'))->update(['notes' => $notes]);
    }

    /**
     * Get a paginated list of charges.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(Session $session, int $page = 0): Collection
    {
        $charges = $this->tenantService->tontine()->charges()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $charges->take($this->tenantService->getLimit());
            $charges->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $charges->get()->each(function($charge) use($session) {
            $settlementService = $charge->is_fee ? $this->feeService : $this->fineService;
            $charge->members_count = $settlementService->getMemberCount($charge, $session);
            $charge->members_paid = $settlementService->getMemberCount($charge, $session, true);
        });
    }

    /**
     * Get the number of charges.
     *
     * @return int
     */
    public function getChargeCount(): int
    {
        return $this->tenantService->tontine()->charges()->count();
    }

    /**
     * Get a paginated list of fees.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFees(Session $session, int $page = 0): Collection
    {
        $fees = $this->tenantService->tontine()->charges()->fee()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fees->take($this->tenantService->getLimit());
            $fees->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $fees->get();
    }

    /**
     * Get the number of fees.
     *
     * @return int
     */
    public function getFeeCount(): int
    {
        return $this->tenantService->tontine()->charges()->fee()->count();
    }

    /**
     * Get a paginated list of fines.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFines(Session $session, int $page = 0): Collection
    {
        $fines = $this->tenantService->tontine()->charges()->fine()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fines->take($this->tenantService->getLimit());
            $fines->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $fines->get();
    }

    /**
     * Get the number of fines.
     *
     * @return int
     */
    public function getFineCount(): int
    {
        return $this->tenantService->tontine()->charges()->fine()->count();
    }

    /**
     * Get the receivables of a given pool.
     *
     * Will return extended data on subscriptions.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getFigures(Pool $pool): array
    {
        return $this->planningService->getFigures($pool);
    }

    /**
     * Get pools summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getPoolsSummary(Session $session): array
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

    /**
     * Get settlements summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getFeesSummary(Session $session): array
    {
        $settlementSum = 0;
        $settlementAmounts = $session->feeSettlementAmounts()->get()
            ->each(function($settlement) use(&$settlementSum) {
                $settlementSum += $settlement->amount;
                $settlement->amount = Currency::format($settlement->amount);
            })->pluck('amount', 'charge_id');

        return [
            'settlements' => $settlementAmounts,
            'sum' => [
                'settlements' => Currency::format($settlementSum),
            ],
        ];
    }

    /**
     * Get settlements summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getFinesSummary(Session $session): array
    {
        $settlementSum = 0;
        $settlementAmounts = $session->fineSettlementAmounts()->get()
            ->each(function($settlement) use(&$settlementSum) {
                $settlementSum += $settlement->amount;
                $settlement->amount = Currency::format($settlement->amount);
            })->pluck('amount', 'charge_id');

        return [
            'settlements' => $settlementAmounts,
            'sum' => [
                'settlements' => Currency::format($settlementSum),
            ],
        ];
    }
}
