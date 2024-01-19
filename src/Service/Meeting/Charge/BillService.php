<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function strtolower;
use function trim;

class BillService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * @param Charge $charge
     *
     * @return string
     */
    private function getBillRelation(Charge $charge): string
    {
        // The intermediate relation to reach the member model.
        return $charge->is_variable ? 'libre_bill' :
            ($charge->period_session ? 'session_bill' :
            ($charge->period_round ? 'round_bill' : 'tontine_bill'));
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param Closure|null $memberFilter
     *
     * @return Builder
     */
    private function getLibreBillsQuery(Charge $charge, Session $session, ?Closure $memberFilter)
    {
        return Bill::with('libre_bill.member')
            ->whereHas('libre_bill', function(Builder $query) use($charge, $session, $memberFilter) {
                $query->where('charge_id', $charge->id)
                    ->when($memberFilter !== null, $memberFilter)
                    ->whereHas('session', function($query) use($session) {
                        return $query->where('round_id', $session->round_id);
                    });
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param Closure|null $memberFilter
     *
     * @return Builder
     */
    private function getSessionBillsQuery(Charge $charge, Session $session, ?Closure $memberFilter)
    {
        return Bill::with('session_bill.member')
            ->whereHas('session_bill', function(Builder $query) use($charge, $session, $memberFilter) {
                $query->where('charge_id', $charge->id)
                    ->when($memberFilter !== null, $memberFilter)
                    ->where('session_id', $session->id);
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param Closure|null $memberFilter
     *
     * @return Builder
     */
    private function getRoundBillsQuery(Charge $charge, Session $session, ?Closure $memberFilter)
    {
        return Bill::with('round_bill.member')
            ->whereHas('round_bill', function(Builder $query) use($charge, $session, $memberFilter) {
                $query->where('charge_id', $charge->id)
                    ->when($memberFilter !== null, $memberFilter)
                    ->where('round_id', $session->round_id);
            });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param Closure|null $memberFilter
     *
     * @return Builder
     */
    private function getTontineBillsQuery(Charge $charge, Session $session, ?Closure $memberFilter)
    {
        return Bill::with('tontine_bill.member')
            ->whereHas('tontine_bill', function(Builder $query) use($charge, $memberFilter) {
                $query->where('charge_id', $charge->id)
                    ->when($memberFilter !== null, $memberFilter);
            })
            ->where(function(Builder $query) use($session) {
                // The bills that are not yet paid, or that are paid in this round.
                $query->orWhere(function(Builder $query) {
                    $query->whereDoesntHave('settlement');
                })->orWhere(function(Builder $query) use($session) {
                    $query->whereHas('settlement', function(Builder $query) use($session) {
                        $query->whereHas('session', function(Builder $query) use($session) {
                            $query->where('round_id', $session->round_id);
                        });
                    });
                });
            });
    }

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
        $memberFilter = !$search ? null : function($query) use($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->whereHas('member', function($query) use($search) {
                $query->where(DB::raw('lower(members.name)'), 'like', $search);
            });
        };
        $billsQuery = match(true) {
            $charge->is_variable => $this->getLibreBillsQuery($charge, $session, $memberFilter),
            $charge->period_session => $this->getSessionBillsQuery($charge, $session, $memberFilter),
            $charge->period_round => $this->getRoundBillsQuery($charge, $session, $memberFilter),
            default => $this->getTontineBillsQuery($charge, $session, $memberFilter),
        };
        return $billsQuery
            ->when($onlyPaid === false, function($query) {
                return $query->whereDoesntHave('settlement');
            })
            ->when($onlyPaid === true, function($query) use($session) {
                return $query->whereHas('settlement', function(Builder $query) use($session) {
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
        $billRelation = $this->getBillRelation($charge);

        return $this->getBillsQuery($charge, $session, $search, $onlyPaid)
            ->with('settlement')
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('id', 'asc')
            ->get()
            ->each(function($bill) use($billRelation, $charge) {
                $bill->member = $bill->$billRelation->member;
                $bill->session = $charge->is_variable ? $bill->libre_bill->session : null;
            });
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
}
