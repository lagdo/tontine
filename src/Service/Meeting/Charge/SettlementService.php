<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\ProfitTransfer;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

use function collect;
use function trans;

class SettlementService
{
    /**
     * @param TenantService $tenantService
     * @param SearchSanitizer $searchSanitizer
     * @param BillService $billService
     * @param PaymentServiceInterface $paymentService;
     */
    public function __construct(private TenantService $tenantService,
        private SearchSanitizer $searchSanitizer, private BillService $billService,
        private PaymentServiceInterface $paymentService)
    {}

    /**
     * @param Round $round
     *
     * @return Collection
     */
    public function getFunds(Round $round): Collection
    {
        return $round->funds()->real()
            ->join('fund_defs', 'fund_defs.id', '=', 'funds.def_id')
            ->orderBy('fund_defs.type') // The default fund is first in the list.
            ->orderBy('funds.id')
            ->get()
            ->pluck('title', 'id');
    }

    /**
     * @param Round $round
     * @param int $fundId
     *
     * @return Fund|null
     */
    public function getFund(Round $round, int $fundId): Fund|null
    {
        return $round->funds()->real()->find($fundId);
    }

    /**
     * @param Collection $bills
     * @param Session $session
     * @param Fund|null $fund
     *
     * @return int
     */
    private function _createSettlements(Collection $bills, Session $session, Fund|null $fund): int
    {
        // Todo: use one insert query
        return DB::transaction(function() use($bills, $session, $fund) {
            $balances = $fund === null ? null:
                ProfitTransfer::query()
                    ->whereFund($fund)
                    ->whereIn('member_id', $bills->pluck('member_id'))
                    ->select('member_id', DB::raw('sum(amount*coef) as value'))
                    ->groupBy('member_id')
                    ->get()
                    ->pluck('value', 'member_id');
            $cancelled = 0;

            foreach($bills as $bill)
            {
                $settlement = new Settlement();
                $settlement->bill()->associate($bill);
                $settlement->session()->associate($session);
                if($fund !== null)
                {
                    // When the settlement amount is transfered from a fund,
                    // skip the bill if the fund doesn't have enough balance.
                    if($bill->amount > ($balances[$bill->member_id] ?? 0))
                    {
                        $cancelled++;
                        continue;
                    }

                    $settlement->fund()->associate($fund);
                }
                $settlement->save();
            }

            return $cancelled;
        });
    }

    /**
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $billId
     * @param Fund|null $fund
     *
     * @return int
     */
    public function createSettlement(Charge $charge, Session $session,
        int $billId, Fund|null $fund = null): int
    {
        $bill = $this->billService->getBill($charge, $session, $billId);
        // Return if the bill is not found or the bill is already settled.
        if(!$bill || ($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }

        return $this->_createSettlements(collect([$bill]), $session, $fund);
    }

    /**
     * Delete a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    public function deleteSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->billService->getBill($charge, $session, $billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }
        if((!$this->paymentService->isEditable($bill->settlement)))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $bill->settlement()->where('session_id', $session->id)->delete();
    }

    /**
     * Create a settlement for all unpaid bills
     *
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param Fund|null $fund
     *
     * @return int
     */
    public function createAllSettlements(Charge $charge, Session $session,
        string $search, Fund|null $fund = null): int
    {
        $bills = $this->billService->getBills($charge, $session, $search, false);
        return $bills->count() === 0 ? 0 :
            $this->_createSettlements($bills, $session, $fund);
    }

    /**
     * Delete all settlements
     *
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     *
     * @return void
     */
    public function deleteAllSettlements(Charge $charge, Session $session, string $search): void
    {
        $bills = $this->billService->getBills($charge, $session, $search, true)
            ->filter(fn($bill) => $this->paymentService->isEditable($bill->settlement));
        if($bills->count() > 0)
        {
            Settlement::whereIn('bill_id', $bills->pluck('id'))
                ->where('session_id', $session->id)
                ->delete();
        }
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return array<int>
     */
    public function getSettlementTotal(Charge $charge, Session $session): array
    {
        $total = DB::table('v_settlements')
            ->where('session_id', $session->id)
            ->where('charge_id', $charge->id)
            ->select(DB::raw('count(*) as count'), DB::raw('sum(amount) as amount'))
            ->first();
        return [$total->count ?? 0, $total->amount ?? 0];
    }
}
