<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\Tontine\TenantService;

class FineReportService
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
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        return Bill::select('fine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('fine_bills.session_id', $session->id)
            ->groupBy('fine_bills.charge_id')
            ->get();
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
        return Bill::select('fine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('fine_bills.session_id', $sessionIds)
            ->groupBy('fine_bills.charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionSettlements(Session $session): Collection
    {
        // Count the session bills
        $query = Settlement::select('fine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('fine_bills.session_id', $session->id)
            ->groupBy('fine_bills.charge_id');
        return $query->get();
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
        $query = Settlement::select('fine_bills.charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('fine_bills.session_id', $sessionIds)
            ->groupBy('fine_bills.charge_id');
        return $query->get();
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
