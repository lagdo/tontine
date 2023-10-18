<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\LibreBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function trans;
use function strtolower;

class LibreFeeService
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
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        return DB::table('libre_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'libre_bills.bill_id', '=', 'bills.id')
            ->where('libre_bills.session_id', $session->id)
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
        $sessionIds = $this->tenantService->getSessionIds($session, false);
        return DB::table('libre_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'libre_bills.bill_id', '=', 'bills.id')
            ->whereIn('libre_bills.session_id', $sessionIds)
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
        $query = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
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
        $sessionIds = $this->tenantService->getSessionIds($session, false);
        $query = DB::table('settlements')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
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
     * @param string $search
     * @param bool $filter|null
     *
     * @return mixed
     */
    private function getMembersQuery(Charge $charge, Session $session,
        string $search = '', ?bool $filter)
    {
        $filterFunction = function($query) use($charge, $session) {
            $query->where('charge_id', $charge->id)->where('session_id', $session->id);
        };

        return $this->tenantService->tontine()->members()->active()
            ->when($search !== '', function($query) use($search) {
                return $query->where(DB::raw('lower(name)'), 'like', '%' . strtolower($search) . '%');
            })
            ->when($filter === false, function($query) use($filterFunction) {
                return $query->whereDoesntHave('libre_bills', $filterFunction);
            })
            ->when($filter === true, function($query) use($filterFunction) {
                $query->whereHas('libre_bills', $filterFunction);
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $filter|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Charge $charge, Session $session,
        string $search = '', ?bool $filter = null, int $page = 0): Collection
    {
        return $this->getMembersQuery($charge, $session, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->with([
                'libre_bills' => function($query) use($charge, $session) {
                    $query->where('charge_id', $charge->id)->where('session_id', $session->id);
                },
            ])
            ->orderBy('name', 'asc')->get()
            ->each(function($member) {
                $member->bill = $member->libre_bills->count() > 0 ?
                    $member->libre_bills->first()->bill : null;
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $filter|null
     *
     * @return int
     */
    public function getMemberCount(Charge $charge, Session $session,
        string $search = '', ?bool $filter = null): int
    {
        return $this->getMembersQuery($charge, $session, $search, $filter)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     * @param int $amount
     *
     * @return void
     */
    public function createBill(Charge $charge, Session $session, int $memberId, int $amount = 0): void
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
                'lendable' => $charge->lendable,
                'issued_at' => now(),
            ]);
            $libreBill = new LibreBill();
            $libreBill->charge()->associate($charge);
            $libreBill->member()->associate($member);
            $libreBill->session()->associate($session);
            $libreBill->bill()->associate($bill);
            $libreBill->save();
        });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return LibreBill|null
     */
    public function getBill(Charge $charge, Session $session, int $memberId): ?LibreBill
    {
        if(!($member = $this->tenantService->tontine()->members()->find($memberId)))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        return LibreBill::with(['bill'])
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
    public function deleteBill(Charge $charge, Session $session, int $memberId): void
    {
        if(!($libreBill = $this->getBill($charge, $session, $memberId)))
        {
            return; // throw new MessageException(trans('tontine.bill.errors.not_found'));
        }

        DB::transaction(function() use($libreBill) {
            $billId = $libreBill->bill_id;
            $libreBill->delete();
            Bill::where('id', $billId)->delete();
        });
    }
}
