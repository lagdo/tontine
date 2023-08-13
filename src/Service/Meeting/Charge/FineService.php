<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function trans;

class FineService
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
     * Get a paginated list of fines.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFines(int $page = 0): Collection
    {
        return $this->tenantService->tontine()->charges()
            ->variable()->orderBy('id', 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of fines.
     *
     * @return int
     */
    public function getFineCount(): int
    {
        return $this->tenantService->tontine()->charges()->variable()->count();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        return DB::table('fine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('fine_bills.session_id', $session->id)
            ->groupBy('charge_id')
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
        $sessionIds = $this->tenantService->getPreviousSessions($session, false);
        return DB::table('fine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('fine_bills.session_id', $sessionIds)
            ->groupBy('charge_id')
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
        $query = DB::table('settlements')->select('charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->groupBy('charge_id');
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
        $sessionIds = $this->tenantService->getPreviousSessions($session, false);
        $query = DB::table('settlements')->select('charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->groupBy('charge_id');
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
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return mixed
     */
    private function getMembersQuery(Charge $charge, Session $session, ?bool $onlyFined)
    {
        $filterFunction = function($query) use($charge, $session) {
            $query->where('charge_id', $charge->id)->where('session_id', $session->id);
        };

        return $this->tenantService->tontine()->members()
            ->when($onlyFined === false, function($query) use($filterFunction) {
                return $query->whereDoesntHave('fine_bills', $filterFunction);
            })
            ->when($onlyFined === true, function($query) use($filterFunction) {
                $query->whereHas('fine_bills', $filterFunction);
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Charge $charge, Session $session, ?bool $onlyFined = null, int $page = 0): Collection
    {
        return $this->getMembersQuery($charge, $session, $onlyFined)
            ->page($page, $this->tenantService->getLimit())
            ->with([
                'fine_bills' => function($query) use($charge, $session) {
                    $query->where('charge_id', $charge->id)->where('session_id', $session->id);
                },
            ])
            ->orderBy('name', 'asc')->get()
            ->each(function($member) {
                $member->bill = $member->fine_bills->count() > 0 ? $member->fine_bills->first()->bill : null;
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return int
     */
    public function getMemberCount(Charge $charge, Session $session, ?bool $onlyFined = null): int
    {
        return $this->getMembersQuery($charge, $session, $onlyFined)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     * @param int $amount
     *
     * @return void
     */
    public function createFine(Charge $charge, Session $session, int $memberId, int $amount = 0): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        DB::transaction(function() use($charge, $session, $member, $amount) {
            $bill = Bill::create([
                'charge' => $charge->name,
                'amount' => $charge->has_amount ? $charge->amount : $amount,
                'issued_at' => now(),
            ]);
            $fine = new FineBill();
            $fine->charge()->associate($charge);
            $fine->member()->associate($member);
            $fine->session()->associate($session);
            $fine->bill()->associate($bill);
            $fine->save();
        });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return FineBill|null
     */
    public function getBill(Charge $charge, Session $session, int $memberId): ?FineBill
    {
        if(!($member = $this->tenantService->tontine()->members()->find($memberId)))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        return FineBill::with(['bill'])
            ->where('charge_id', $charge->id)
            ->where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return void
     */
    public function deleteFine(Charge $charge, Session $session, int $memberId): void
    {
        if(!($fine = $this->getBill($charge, $session, $memberId)))
        {
            return; // throw new MessageException(trans('tontine.bill.errors.not_found'));
        }

        DB::transaction(function() use($fine) {
            $billId = $fine->bill_id;
            $fine->delete();
            Bill::where('id', $billId)->delete();
        });
    }
}
