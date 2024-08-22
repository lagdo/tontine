<?php

namespace App\Ajax\Web\Meeting\Charge\Libre;

use App\Ajax\ChargeCallable;
use App\Ajax\Web\Meeting\Charge\LibreFee as Charge;

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
        $this->bag('meeting')->set('settlement.libre.search', '');
        $this->bag('meeting')->set('settlement.libre.filter', null);

        $html = $this->renderView('pages.meeting.settlement.home', [
            'type' => 'libre',
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fees-libre', $html);
        $this->jq('#btn-fee-libre-settlements-back')->click($this->rq(Charge::class)->home());
        $this->jq('#btn-fee-libre-settlements-filter')->click($this->rq()->toggleFilter());

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
        $search = trim($this->bag('meeting')->get('settlement.libre.search', ''));
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        $billCount = $this->billService->getBillCount($this->charge,
            $this->session, $search, $onlyUnpaid);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber,
            $billCount, 'meeting', 'settlement.page');
        $bills = $this->billService->getBills($this->charge, $this->session,
            $search, $onlyUnpaid, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $billCount);

        $html = $this->renderView('pages.meeting.settlement.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'bills' => $bills,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-libre-bills', $html);
        $this->response->call('makeTableResponsive', 'meeting-fee-libre-bills');

        $billId = jq()->parent()->attr('data-bill-id')->toInt();
        $this->jq('.btn-add-settlement', '#meeting-fee-libre-bills')
            ->click($this->rq()->addSettlement($billId));
        $this->jq('.btn-del-settlement', '#meeting-fee-libre-bills')
            ->click($this->rq()->delSettlement($billId));
        $this->jq('.btn-edit-notes', '#meeting-fee-libre-bills')
            ->click($this->rq()->editNotes($billId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.libre.filter', $onlyUnpaid);

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
}
