<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

class LibreFeeService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService)
    {}

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
            ->variable()->orderBy('id', 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of fees.
     *
     * @return int
     */
    public function getFeeCount(): int
    {
        return $this->tenantService->tontine()->charges()->variable()->count();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getSessionBills(Session $session): Collection
    {
        return Bill::ofSession($session)->libre()
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->groupBy('charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getSessionSettlements(Session $session): Collection
    {
        return Bill::ofSession($session)->libre()
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->whereHas('settlement', function($query) use($session) {
                $query->where('session_id', $session->id);
            })
            ->groupBy('charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getRoundBills(Session $session): Collection
    {
        return Bill::ofRound($session)->libre()
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->groupBy('charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getRoundSettlements(Session $session): Collection
    {
        return Bill::ofRound($session)->libre()
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->whereHas('settlement', function($query) use($session) {
                $query->whereHas('session', function($query) use($session) {
                    $query->where('id', '!=', $session->id)
                        ->where('round_id', $session->round_id);
                });
            })
            ->groupBy('charge_id')
            ->get();
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
        $sessionBills = $this->getSessionBills($session);
        $roundBills = $this->getRoundBills($session);
        return [
            'total' => [
                'session' => $sessionBills->pluck('total', 'charge_id'),
                'round' => $roundBills->pluck('total', 'charge_id'),
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
        $sessionSettlements = $this->getSessionSettlements($session);
        $roundSettlements = $this->getRoundSettlements($session);
        return [
            'total' => [
                'session' => $sessionSettlements->pluck('total', 'charge_id'),
                'round' => $roundSettlements->pluck('total', 'charge_id'),
            ],
            'amount' => [
                'session' => $sessionSettlements->pluck('amount', 'charge_id'),
                'round' => $roundSettlements->pluck('amount', 'charge_id'),
            ],
        ];
    }
}
