<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class SettlementService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
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
     * @param int $chargeId
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->find($chargeId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     * @param int $page
     *
     * @return Collection
     */
    abstract public function getMembers(Charge $charge, Session $session, ?bool $onlyPaid = null, int $page = 0): Collection;

    /**
     * Get the number of members in the selected round.
     *
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return int
     */
    abstract public function getMemberCount(Charge $charge, Session $session, ?bool $onlyPaid = null): int;

    /**
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    abstract public function createSettlement(Charge $charge, Session $session, int $Id): void;

    /**
     * Delete a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    abstract public function deleteSettlement(Charge $charge, Session $session, int $Id): void;

    /**
     * @param Session $session
     *
     * @return mixed
     */
    private function getSettlementCountQuery(Session $session)
    {
        return Settlement::join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('sessions', 'settlements.session_id', '=', 'sessions.id')
            ->groupBy('bills.charge_id')
            ->select('bills.charge_id', DB::raw('count(*) as total'));
    }

    /**
     * Get the numbers of settlements
     *
     * @param Session $session
     * @param bool $withPrevious
     *
     * @return Collection
     */
    public function getSettlementCount(Session $session, bool $withPrevious): Collection
    {
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');
        $query = $withPrevious ?
            $this->getSettlementCountQuery($session)->whereIn('sessions.id', $sessionIds) :
            $this->getSettlementCountQuery($session)->where('sessions.id', $session->id);
        return $query->pluck('total', 'charge_id');
    }

    /**
     * Get the numbers of bills
     *
     * @param Session $session
     * @param bool $withPrevious
     *
     * @return Collection
     */
    public function getBillCount(Session $session, bool $withPrevious): Collection
    {
        $chargeIds = $this->tenantService->tontine()->charges()->pluck('id');
        $noSessionQuery = Bill::whereIn('charge_id', $chargeIds)
            ->whereNull('session_id')
            ->select('charge_id', DB::raw('count(*) as total'))
            ->groupBy('charge_id');
        $sessionQuery = Bill::whereIn('charge_id', $chargeIds)
            ->select('bills.charge_id', DB::raw('count(*) as total'))
            ->groupBy('bills.charge_id')
            ->join('sessions', 'bills.session_id', '=', 'sessions.id');
        $sessionQuery = $withPrevious ?
            $sessionQuery->where('sessions.start_at', '<=', $session->start_at) :
            $sessionQuery->where('sessions.id', $session->id);
        return $noSessionQuery->union($sessionQuery)->pluck('total', 'charge_id');
    }

    /**
     * Get a formatted amount.
     *
     * @param int $amount
     * @param bool $hideSymbol
     *
     * @return string
     */
    public function getFormattedAmount(int $amount, bool $hideSymbol = false): string
    {
        return Currency::format($amount, $hideSymbol);
    }
}
