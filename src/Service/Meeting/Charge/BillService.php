<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;

use function strtolower;

class BillService
{
    /**
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     */
    public function __construct(protected TenantService $tenantService,
        protected SessionService $sessionService)
    {}

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $relation
     * @param string $search
     * @param bool $onlyPaid|null
     *
     * @return Builder
     */
    private function getBillsQuery(Charge $charge, Session $session,
        string $relation, string $search, ?bool $onlyPaid): Builder|Relation
    {
        $relationFilter = function(Builder $query) use($charge, $session, $relation) {
            $query->where('charge_id', $charge->id)
                ->when($relation === 'session_bill', function($query) use($session) {
                    // Filter session bills only on current session
                    return $query->where('session_id', $session->id);
                })
                ->when($relation === 'libre_bill', function($query) use($session) {
                    // Filter libre bills on current and previous sessions
                    $sessionIds = $this->sessionService->getRoundSessionIds($session);
                    return $query->whereIn('session_id', $sessionIds);
                });
        };
        $allBillsFilter = function($query) use($session) {
            return $query->where(function(Builder $query) use($session) {
                // The bills that are paid in this session, or that are not yet paid.
                $query->orWhere(function(Builder $query) {
                    $query->whereDoesntHave('settlement');
                })->orWhere(function(Builder $query) use($session) {
                    $query->whereHas('settlement', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                });
            });
        };
        $searchFilter = function($query) use($search, $relation) {
            $relationTable = $relation . 's';
            $search = '%' . strtolower($search) . '%';
            return $query->select('bills.*')
                ->join($relationTable, "$relationTable.bill_id", '=', 'bills.id')
                ->join('members', "$relationTable.member_id", '=', 'members.id')
                ->where(DB::raw('lower(members.name)'), 'like', $search);
        };

        return Bill::whereHas($relation, $relationFilter)
            ->when($onlyPaid === false, function($query) {
                return $query->whereDoesntHave('settlement');
            })
            ->when($onlyPaid === true, function($query) use($session) {
                return $query->whereHas('settlement', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id);
                });
            })
            ->when($onlyPaid === null && $relation !== 'session_bill', $allBillsFilter)
            ->when($search !== '', $searchFilter);
    }

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
        $billRelations = !$charge->is_variable ? [$billRelation . '.member', 'settlement'] :
            ['libre_bill.session', 'libre_bill.member', 'settlement'];
        $query = $this->getBillsQuery($charge, $session, $billRelation, $search, $onlyPaid);

        return $query->with($billRelations)
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
     * @param string $search
     * @param bool $onlyPaid|null
     *
     * @return int
     */
    public function getBillCount(Charge $charge, Session $session,
        string $search = '', ?bool $onlyPaid = null): int
    {
        $billRelation = $this->getBillRelation($charge);

        return $this->getBillsQuery($charge, $session, $billRelation, $search, $onlyPaid)
            ->count();
    }
}
