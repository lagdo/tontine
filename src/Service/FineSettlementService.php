<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Model\Session;

use Illuminate\Support\Collection;

use function now;

class FineSettlementService extends SettlementService
{
    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return mixed
     */
    private function getQuery(Charge $charge, Session $session, ?bool $onlyPaid)
    {
        // $query = $this->tenantService->round()->bills()->with(['member']);
        $query = $session->bills()->with(['member']);
        if($onlyPaid === false)
        {
            $query->whereDoesntHave('settlements');
        }
        elseif($onlyPaid === true)
        {
            $query->whereHas('settlements');
        }
        return $query->withCount(['settlements'])->where('charge_id', $charge->id);
    }

    /**
     * @inheritDoc
     */
    public function getMembers(Charge $charge, Session $session, ?bool $onlyPaid = null, int $page = 0): Collection
    {
        $bills = $this->getQuery($charge, $session, $onlyPaid);
        if($page > 0 )
        {
            $bills->take($this->tenantService->getLimit());
            $bills->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $bills->get()->map(function($bill) {
            $member = $bill->member;
            $member->bill = $bill;
            $member->paid = $bill->settlements_count > 0;
            return $member;
        });
    }

    /**
     * @inheritDoc
     */
    public function getMemberCount(Charge $charge, Session $session, ?bool $onlyPaid = null): int
    {
        return $this->getQuery($charge, $session, $onlyPaid)->count();
    }

    /**
     * @inheritDoc
     */
    public function createSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->getQuery($charge, $session, false)->with(['member'])->find($billId);
        $settlement = $bill->settlements()->first();
        if(($settlement))
        {
            return;
        }
        $settlement = new Settlement();
        $settlement->paid_at = now();
        $settlement->member()->associate($bill->member);
        $settlement->bill()->associate($bill);
        $settlement->save();
    }

    /**
     * @inheritDoc
     */
    public function deleteSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->getQuery($charge, $session, true)->with(['member'])->find($billId);
        $settlement = $bill->settlements()->first();
        if(!$settlement)
        {
            return;
        }
        $bill->settlements()->delete();
    }
}
