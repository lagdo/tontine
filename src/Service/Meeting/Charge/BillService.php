<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\LibreBill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function strtolower;
use function trans;
use function trim;

class BillService
{
    /**
     * @param SettlementTargetService $targetService
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     */
    public function __construct(protected SettlementTargetService $targetService,
        protected TenantService $tenantService, protected LocaleService $localeService)
    {}

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getBillsQuery(Charge $charge, Session $session,
        string $search = '', ?bool $onlyPaid = null): Builder|Relation
    {
        $search = trim($search);
        return Bill::ofSession($session)->with('session')
            ->where('charge_id', $charge->id)
            ->when($search !== '', function($query) use($search) {
                $query->where(DB::raw('lower(member)'), 'like', $search);
            })
            ->when($onlyPaid === false, function($query) {
                $query->unpaid();
            })
            ->when($onlyPaid === true, function($query) use($session) {
                $query->whereHas('settlement', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id);
                });
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $onlyPaid|null
     *
     * @return int
     */
    public function getBillCount(Charge $charge, Session $session,
        string $search = '', ?bool $onlyPaid = null): int
    {
        return $this->getBillsQuery($charge, $session, $search, $onlyPaid)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $onlyPaid|null
     * @param int $page
     *
     * @return Collection
     */
    public function getBills(Charge $charge, Session $session,
        string $search = '', ?bool $onlyPaid = null, int $page = 0): Collection
    {
        return $this->getBillsQuery($charge, $session, $search, $onlyPaid)
            ->with('settlement')
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('member', 'asc')
            ->orderBy('bill_date', 'asc')
            ->get();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $billId
     *
     * @return Bill|null
     */
    public function getBill(Charge $charge, Session $session, int $billId): ?Bill
    {
        return $this->getBillsQuery($charge, $session)->find($billId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return Bill
     */
    public function getSettlementCount(Charge $charge, Session $session): Bill
    {
        return $this->getBillsQuery($charge, $session, '', true)
            ->select(DB::raw('count(*) as total'), DB::raw('sum(bills.amount) as amount'))
            ->first();
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
        string $search = '', ?bool $filter = null): Builder|Relation
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
                'libre_bills' => fn($query) => $query
                    ->where('charge_id', $charge->id)->where('session_id', $session->id),
            ])
            ->orderBy('name', 'asc')
            ->get()
            ->each(function($member) {
                $member->remaining = 0;
                $member->bill = $member->libre_bills->first()?->bill ?? null;
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
     *
     * @return Member|null
     */
    public function getMember(Charge $charge, Session $session, int $memberId): ?Member
    {
        $members = $this->getMembersQuery($charge, $session)
            ->where('id', $memberId)
            ->with([
                'libre_bills.bill',
                'libre_bills' => fn($query) => $query
                    ->where('charge_id', $charge->id)->where('session_id', $session->id),
            ])
            ->get();
        if($members->count() === 0)
        {
            return null;
        }

        $member = $members->first();
        $member->bill = $member->libre_bills->first()?->bill ?? null;
        $member->remaining = 0;
        // Check if there is a settlement target.
        if(($target = $this->targetService->getTarget($charge, $session)) !== null)
        {
            $settlements = $this->targetService->getMembersSettlements($charge, $target, $members);
            $paid = $settlements[$member->id] ?? 0;
            $member->remaining = $target->amount > $paid ? $target->amount - $paid : 0;
        }
        return $member;
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
    public function getMemberBill(Charge $charge, Session $session, int $memberId): ?LibreBill
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
        if(!($libreBill = $this->getMemberBill($charge, $session, $memberId)))
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
        if(!($libreBill = $this->getMemberBill($charge, $session, $memberId)))
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
