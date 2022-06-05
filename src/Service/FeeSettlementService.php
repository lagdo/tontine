<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Model\Session;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use function now;

class FeeSettlementService extends SettlementService
{
    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return Bill|null
     */
    private function getBill(Charge $charge, Session $session): ?Bill
    {
        $bills = $charge->bills();
        if($charge->period === Charge::PERIOD_SESSION)
        {
            $bills->where('session_id', $session->id);
        }
        elseif($charge->period === Charge::PERIOD_ROUND)
        {
            $bills->where('round_id', $this->tenantService->round()->id);
        }
        // if($charge->period === Charge::PERIOD_ONCE)
        return $bills->first();;
    }

    /**
     * @param Bill $bill
     * @param bool $onlyPaid|null
     *
     * @return mixed
     */
    private function getQuery(Bill $bill, ?bool $onlyPaid)
    {
        $query = $this->tenantService->tontine()->members();
        if($onlyPaid === false)
        {
            $query->whereDoesntHave('settlements', function(Builder $query) use($bill) {
                $query->where('bill_id', $bill->id);
            });
        }
        elseif($onlyPaid === true)
        {
            $query->whereHas('settlements', function(Builder $query) use($bill) {
                $query->where('bill_id', $bill->id);
            });
        }
        return $query->withCount(['settlements' => function(Builder $query) use($bill) {
            $query->where('bill_id', $bill->id);
        }]);
    }

    /**
     * @inheritDoc
     */
    public function getMembers(Charge $charge, Session $session, ?bool $onlyPaid = null, int $page = 0): Collection
    {
        $bill = $this->getBill($charge, $session);
        $members = $this->getQuery($bill, $onlyPaid);
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $members->get()->each(function($member) use($bill) {
            $member->bill = $bill;
            $member->paid = $member->settlements_count > 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function getMemberCount(Charge $charge, Session $session, ?bool $onlyPaid = null): int
    {
        return $this->getQuery($this->getBill($charge, $session), $onlyPaid)->count();
    }

    /**
     * @param Bill $bill
     * @param int $memberId
     *
     * @return array
     */
    private function getSettlement(Bill $bill, int $memberId): array
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        return [$member, $member->settlements()->where('bill_id', $bill->id)->first()];
    }

    /**
     * @inheritDoc
     */
    public function createSettlement(Charge $charge, Session $session, int $memberId): void
    {
        $bill = $this->getBill($charge, $session);
        [$member, $settlement] = $this->getSettlement($bill, $memberId);
        if(($settlement))
        {
            return;
        }
        $settlement = new Settlement();
        $settlement->paid_at = now();
        $settlement->member()->associate($member);
        $settlement->bill()->associate($bill);
        $settlement->save();
    }

    /**
     * @inheritDoc
     */
    public function deleteSettlement(Charge $charge, Session $session, int $memberId): void
    {
        $bill = $this->getBill($charge, $session);
        [$member, $settlement] = $this->getSettlement($bill, $memberId);
        if(!$settlement)
        {
            return;
        }
        $member->settlements()->where('bill_id', $bill->id)->delete();
    }
}
