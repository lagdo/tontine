<?php

namespace App\Ajax\Web\Meeting\Charge\Fixed;

use App\Ajax\CallableChargeClass;
use App\Ajax\Web\Meeting\Charge\FixedFee as Charge;

use function Jaxon\jq;
use function trim;

class Settlement extends CallableChargeClass
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

        $html = $this->render('pages.meeting.settlement.home', [
            'type' => 'fixed',
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fees-fixed', $html);
        $this->jq('#btn-fee-fixed-settlements-back')
            ->click($this->rq(Charge::class)->home());
        $this->jq('#btn-fee-fixed-settlements-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
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
        $settlement = $this->settlementService->getSettlementCount($this->charge, $this->session);

        $html = $this->render('pages.meeting.settlement.page', [
            'type' => 'fixed',
            'search' => $search,
            'session' => $this->session,
            'charge' => $this->charge,
            'billCount' => $this->billService->getBillCount($this->charge, $this->session),
            'settlement' => $settlement,
            'bills' => $bills,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-fixed-bills', $html);

        $this->jq('.btn-add-all-settlements')->click($this->rq()->addAllSettlements());
        $this->jq('.btn-del-all-settlements')->click($this->rq()->delAllSettlements());
        $billId = jq()->parent()->attr('data-bill-id')->toInt();
        $this->jq('.btn-add-settlement', '#meeting-fee-fixed-bills')
            ->click($this->rq()->addSettlement($billId));
        $this->jq('.btn-del-settlement', '#meeting-fee-fixed-bills')
            ->click($this->rq()->delSettlement($billId));
        $this->jq('.btn-edit-notes', '#meeting-fee-fixed-bills')
            ->click($this->rq()->editNotes($billId));
        $this->jq('#btn-fee-fixed-settlements-search')
            ->click($this->rq()->search(jq('#txt-fee-settlements-search')->val()));

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
