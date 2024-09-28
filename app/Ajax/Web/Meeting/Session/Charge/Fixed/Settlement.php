<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Fixed;

use App\Ajax\ChargeCallable;
use App\Ajax\Web\Meeting\Session\Charge\FixedFee as Charge;

use function Jaxon\jq;
use function trim;

class Settlement extends ChargeCallable
{
    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('charge.id', $chargeId);
        $this->bag('meeting')->set('settlement.fixed.filter', null);
        $this->bag('meeting')->set('settlement.fixed.search', '');

        $html = $this->renderView('pages.meeting.settlement.home', [
            'type' => 'fixed',
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fees-fixed', $html);

        $this->response->jq('#btn-fee-fixed-settlements-back')
            ->click($this->rq(Charge::class)->home());
        $this->response->jq('#btn-fee-fixed-settlements-filter')->click($this->rq()->toggleFilter());
        $this->response->jq('#btn-fee-fixed-settlements-search')
            ->click($this->rq()->search(jq('#txt-fee-settlements-search')->val()));

        return $this->page(1);
    }

    private function showTotal()
    {
        $settlement = $this->settlementService->getSettlementCount($this->charge, $this->session);
        $settlementCount = $settlement->total ?? 0;
        $settlementAmount = $settlement->amount ?? 0;

        $billCount = $this->billService->getBillCount($this->charge, $this->session);
        $html = $this->renderView('pages.meeting.settlement.total', [
            'billCount' => $billCount,
            'settlementCount' => $settlementCount,
            'settlementAmount' => $settlementAmount,
        ]);
        $this->response->html('meeting-settlements-total', $html);

        $html = $this->renderView('pages.meeting.settlement.action', [
            'session' => $this->session,
            'charge' => $this->charge,
            'billCount' => $billCount,
            'settlementCount' => $settlementCount,
        ]);
        $this->response->html('meeting-settlements-action', $html);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('meeting')->get('settlement.fixed.search', ''));
        $onlyUnpaid = $this->bag('meeting')->get('settlement.fixed.filter', null);
        $billCount = $this->billService->getBillCount($this->charge,
            $this->session, $search, $onlyUnpaid);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber,
            $billCount, 'meeting', 'settlement.page');
        $bills = $this->billService->getBills($this->charge, $this->session,
            $search, $onlyUnpaid, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $billCount);

        $this->showTotal();

        $html = $this->renderView('pages.meeting.settlement.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'bills' => $bills,
        ]);
        $this->response->html('meeting-fee-fixed-bills', $html);
        $this->response->js()->makeTableResponsive('meeting-fee-fixed-bills');

        $this->response->jq('.btn-add-all-settlements')->click($this->rq()->addAllSettlements());
        $this->response->jq('.btn-del-all-settlements')->click($this->rq()->delAllSettlements());
        $billId = jq()->parent()->attr('data-bill-id')->toInt();
        $this->response->jq('.btn-add-settlement', '#meeting-fee-fixed-bills')
            ->click($this->rq()->addSettlement($billId));
        $this->response->jq('.btn-del-settlement', '#meeting-fee-fixed-bills')
            ->click($this->rq()->delSettlement($billId));
        $this->response->jq('.btn-edit-notes', '#meeting-fee-fixed-bills')
            ->click($this->rq()->editNotes($billId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.fixed.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.fixed.filter', $onlyUnpaid);

        return $this->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('settlement.fixed.search', trim($search));

        return $this->page();
    }

    /**
     * @before checkChargeEdit
     * @after showBalanceAmounts
     * @param int $billId
     *
     * @return mixed
     */
    public function addSettlement(int $billId)
    {
        $this->settlementService->createSettlement($this->charge, $this->session, $billId);

        return $this->page();
    }

    /**
     * @before checkChargeEdit
     * @after showBalanceAmounts
     * @param int $billId
     *
     * @return mixed
     */
    public function delSettlement(int $billId)
    {
        $this->settlementService->deleteSettlement($this->charge, $this->session, $billId);

        return $this->page();
    }

    /**
     * @before checkChargeEdit
     * @after showBalanceAmounts
     * @return mixed
     */
    public function addAllSettlements()
    {
        $this->settlementService->createAllSettlements($this->charge, $this->session);

        return $this->page();
    }

    /**
     * @before checkChargeEdit
     * @after showBalanceAmounts
     * @return mixed
     */
    public function delAllSettlements()
    {
        $this->settlementService->deleteAllSettlements($this->charge, $this->session);

        return $this->page();
    }
}
