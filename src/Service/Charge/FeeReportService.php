<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class FeeReportService
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
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        $sessionQuery = Bill::select('session_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->where('session_bills.session_id', $session->id)
            ->groupBy('session_bills.charge_id');
        // Count the round bills
        $roundQuery = Bill::select('round_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('round_bills.charge_id');
        // Count the tontine bills only for active members
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        $tontineQuery = Bill::select('tontine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('tontine_bills.charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsBills(Session $session): Collection
    {
        // Count the session bills
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');
        $sessionQuery = Bill::select('session_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->whereIn('session_bills.session_id', $sessionIds)
            ->groupBy('session_bills.charge_id');
        // Count the round bills
        $roundQuery = Bill::select('round_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('round_bills.charge_id');
        // Count the tontine bills only for active members
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        $tontineQuery = Bill::select('tontine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('tontine_bills.charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionSettlements(Session $session): Collection
    {
        // Count the session bills
        $sessionQuery = Settlement::select('session_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->where('session_bills.session_id', $session->id)
            ->groupBy('session_bills.charge_id');
        // Count the round bills
        $roundQuery = Settlement::select('round_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('round_bills.charge_id');
        // Count the tontine bills only for active members
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        $tontineQuery = Settlement::select('tontine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('tontine_bills.charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsSettlements(Session $session): Collection
    {
        // Count the session bills
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');
        $sessionQuery = Settlement::select('session_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('session_bills', 'session_bills.bill_id', '=', 'bills.id')
            ->whereIn('session_bills.session_id', $sessionIds)
            ->groupBy('session_bills.charge_id');
        // Count the round bills
        $roundQuery = Settlement::select('round_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('round_bills', 'round_bills.bill_id', '=', 'bills.id')
            ->where('round_bills.round_id', $session->round_id)
            ->groupBy('round_bills.charge_id');
        // Count the tontine bills only for active members
        $memberIds = $this->tenantService->tontine()->members()->pluck('id');
        $tontineQuery = Settlement::select('tontine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('tontine_bills', 'tontine_bills.bill_id', '=', 'bills.id')
            ->whereIn('tontine_bills.member_id', $memberIds)
            ->groupBy('tontine_bills.charge_id');
        return $sessionQuery->union($roundQuery)->union($tontineQuery)->get();
    }

    /**
     * Format the amounts in the settlements
     *
     * @param Collection $settlements
     *
     * @return Collection
     */
    private function formatAmounts(Collection $settlements): Collection
    {
        return $settlements->map(function($settlement) {
            $settlement->amount = $this->localeService->formatMoney((int)$settlement->amount);
            return $settlement;
        });
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
            'zero' => $this->localeService->formatMoney(0),
            'total' => [
                'current' => $currentSettlements->pluck('total', 'charge_id'),
                'previous' => $previousSettlements->pluck('total', 'charge_id'),
            ],
            'amount' => [
                'current' => $this->formatAmounts($currentSettlements)->pluck('amount', 'charge_id'),
                'previous' => $this->formatAmounts($previousSettlements)->pluck('amount', 'charge_id'),
            ],
        ];
    }
}
