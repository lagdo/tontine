<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

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
}
