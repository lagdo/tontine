<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class FeeService
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
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
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
        return $this->tenantService->getSession($sessionId);
    }

    /**
     * Get a paginated list of fees.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFees(int $page = 0): Collection
    {
        return $this->tenantService->tontine()->charges()
            ->fixed()->orderBy('id', 'desc')
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the number of fees.
     *
     * @return int
     */
    public function getFeeCount(): int
    {
        return $this->tenantService->tontine()->charges()->fixed()->count();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        $sessionQuery = DB::table('session_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'session_bills.bill_id', '=', 'bills.id')
            ->where('session_bills.session_id', $session->id)
            ->groupBy('charge_id');
        // Count the round bills
        $roundQuery = DB::table('round_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('charge_id');
        // Count the tontine bills only for active members
        $memberIds = $this->tenantService->tontine()->members()->active()->pluck('id');
        $tontineQuery = DB::table('tontine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsBills(Session $session): Collection
    {
        // Count the session bills.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        $sessionQuery = DB::table('session_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'session_bills.bill_id', '=', 'bills.id')
            ->whereIn('session_bills.session_id', $sessionIds)
            ->groupBy('charge_id');
        // Count the round bills.
        $roundQuery = DB::table('round_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('charge_id');
        // Count the tontine bills only for active members.
        $memberIds = $this->tenantService->tontine()->members()->active()->pluck('id');
        $tontineQuery = DB::table('tontine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionSettlements(Session $session): Collection
    {
        // Count the session bills settlements.
        $sessionQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->groupBy('charge_id');
        // Count the round bills settlements.
        $roundQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->groupBy('charge_id');
        // Count the tontine bills settlements.
        $tontineQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->groupBy('charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsSettlements(Session $session): Collection
    {
        // The current and future sessions ids.
        $sessionIds = $this->tenantService->sessions()
            ->where('start_at', '>=', $session->start_at)->pluck('id');
        // Count the session bills settlements.
        $sessionQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->whereNotIn('settlements.session_id', $sessionIds)
            ->groupBy('charge_id');
        // Count the round bills settlements.
        $roundQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->whereNotIn('settlements.session_id', $sessionIds)
            ->groupBy('charge_id');
        // Count the tontine bills settlements.
        $tontineQuery = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereNotIn('settlements.session_id', $sessionIds)
            ->groupBy('charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * Get the report of bills
     *
     * @param Session $session
     *
     * @return array
     */
    public function getBills(Session $session): array
    {
        $currentBills = $this->getCurrentSessionBills($session);
        $previousBills = $this->getPreviousSessionsBills($session);
        return [
            'total' => [
                'current' => $currentBills->pluck('total', 'charge_id'),
                'previous' => $previousBills->pluck('total', 'charge_id'),
            ],
        ];
    }

    /**
     * Get the report of settlements
     *
     * @param Session $session
     *
     * @return array
     */
    public function getSettlements(Session $session): array
    {
        $currentSettlements = $this->getCurrentSessionSettlements($session);
        $previousSettlements = $this->getPreviousSessionsSettlements($session);
        return [
            'total' => [
                'current' => $currentSettlements->pluck('total', 'charge_id'),
                'previous' => $previousSettlements->pluck('total', 'charge_id'),
            ],
            'amount' => [
                'current' => $currentSettlements->pluck('amount', 'charge_id'),
                'previous' => $previousSettlements->pluck('amount', 'charge_id'),
            ],
        ];
    }
}
