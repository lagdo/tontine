<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

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
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $billId
     * @param Fund|null $fund
     *
     * @return void
     */
    public function createSettlement(Charge $charge, Session $session,
        int $billId, Fund|null $fund = null): void
    {
        $bill = $this->billService->getBill($charge, $session, $billId);
        // Return if the bill is not found or the bill is already settled.
        if(!$bill || ($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }
        $settlement = new Settlement();
        $settlement->bill()->associate($bill);
        $settlement->session()->associate($session);
        if($fund !== null)
        {
            $settlement->fund()->associate($fund);
        }
        $settlement->save();
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
     * @return void
     */
    public function createAllSettlements(Charge $charge, Session $session,
        string $search, Fund|null $fund = null): void
    {
        $bills = $this->billService->getBills($charge, $session, $search, false);
        if($bills->count() === 0)
        {
            // Reset the fund
            $this->fund = null;

            return;
        }

        // Todo: use one insert query
        DB::transaction(function() use($bills, $session, $fund) {
            foreach($bills as $bill)
            {
                $settlement = new Settlement();
                $settlement->bill()->associate($bill);
                $settlement->session()->associate($session);
                if($fund !== null)
                {
                    $settlement->fund()->associate($fund);
                }
                $settlement->save();
            }
        });

        // Reset the fund
        $this->fund = null;
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
        if($bills->count() === 0)
        {
            return;
        }

        Settlement::whereIn('bill_id', $bills->pluck('id'))
            ->where('session_id', $session->id)
            ->delete();
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
