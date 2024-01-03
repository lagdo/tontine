<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\LibreBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

use function trans;
use function strtolower;

class LibreFeeService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService,
        protected SettlementTargetService $targetService)
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
        $sessionIds = $this->sessionService->getRoundSessionIds($session, withCurr: false);
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
        $sessionIds = $this->sessionService->getRoundSessionIds($session, withCurr: false);
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
     * @return Builder|Relation
     */
    private function getMembersQuery(Charge $charge, Session $session,
        string $search = '', ?bool $filter): Builder|Relation
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
        $members = $this->getMembersQuery($charge, $session, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->with([
                'libre_bills' => function($query) use($charge, $session) {
                    $query->where('charge_id', $charge->id)->where('session_id', $session->id);
                },
            ])
            ->orderBy('name', 'asc')
            ->get()
            ->each(function($member) {
                $member->remaining = 0;
                $member->bill = $member->libre_bills->count() > 0 ?
                    $member->libre_bills->first()->bill : null;
            });
        // Check if there is a settlement target.
        if(!($target = $this->targetService->getTarget($charge, $session)))
        {
            return $members;
        }

        $settlements = $this->targetService->getMembersSettlements($charge, $target, $members);

        return $members->each(function($member) use($target, $settlements) {
            $member->target = $target->amount;
            $paid = $settlements[$member->id] ?? 0;
            $member->remaining = $target->amount > $paid ? $target->amount - $paid : 0;
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
     * @param bool $paid
     * @param float $amount
     *
     * @return void
     */
    public function createBill(Charge $charge, Session $session,
        int $memberId, bool $paid, float $amount = 0): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        if($amount !== 0)
        {
            $amount = $this->localeService->convertMoneyToInt($amount);
        }
        DB::transaction(function() use($charge, $session, $member, $paid, $amount) {
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
            if($paid)
            {
                $settlement = new Settlement();
                $settlement->bill()->associate($bill);
                $settlement->session()->associate($session);
                $settlement->save();
            }
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

        return LibreBill::with(['bill.settlement'])
            ->where('charge_id', $charge->id)
            ->where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     * @param float $amount
     *
     * @return void
     */
    public function updateBill(Charge $charge, Session $session, int $memberId, float $amount): void
    {
        if(!($libreBill = $this->getBill($charge, $session, $memberId)))
        {
            return; // throw new MessageException(trans('tontine.bill.errors.not_found'));
        }

        $libreBill->bill->update([
            'amount'=> $this->localeService->convertMoneyToInt($amount),
        ]);
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

        DB::transaction(function() use($libreBill, $session) {
            $bill = $libreBill->bill;
            $libreBill->delete();
            if($bill !== null)
            {
                // Delete the settlement only if it is on the same session
                if($bill->settlement !== null && $bill->settlement->session_id === $session->id)
                {
                    $bill->settlement->delete();
                }
                $bill->delete();
            }
        });
    }
}
