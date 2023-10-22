<?php

namespace App\Ajax\Web\Meeting\Charge\Fixed;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use App\Ajax\Web\Meeting\Credit\Loan;
use App\Ajax\Web\Meeting\Charge\FixedFee as Charge;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;

use function Jaxon\jq;
use function trans;
use function trim;

/**
 * @databag meeting
 * @before getCharge
 */
class Settlement extends CallableClass
{
    /**
     * @di
     * @var BillService
     */
    protected BillService $billService;

    /**
     * @di
     * @var SettlementService
     */
    protected SettlementService $settlementService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

    protected function getCharge()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $chargeId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fee.fixed.id');
        $this->session = $this->settlementService->getSession($sessionId);
        $this->charge = $this->settlementService->getCharge($chargeId);
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('fee.fixed.id', $chargeId);
        $this->bag('meeting')->set('settlement.fixed.search', '');
        $this->bag('meeting')->set('settlement.fixed.filter', null);

        $html = $this->view()->render('tontine.pages.meeting.settlement.home', [
            'charge' => $this->charge,
            'type' => 'fixed',
        ]);
        $this->response->html('meeting-fees-fixed', $html);
        $this->jq('#btn-fee-fixed-settlements-back')
            ->click($this->cl(Charge::class)->rq()->home());
        $this->jq('#btn-fee-fixed-settlements-search')
            ->click($this->rq()->search(jq('#txt-fee-settlements-search')->val()));
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
        $settlement = $this->settlementService->getSettlement($this->charge, $this->session);

        $html = $this->view()->render('tontine.pages.meeting.settlement.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'billCount' => $billCount,
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
     * @param int $billId
     *
     * @return mixed
     */
    public function addSettlement(int $billId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->createSettlement($this->charge, $this->session, $billId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    /**
     * @param int $billId
     *
     * @return mixed
     */
    public function delSettlement(int $billId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->deleteSettlement($this->charge, $this->session, $billId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    /**
     * @return mixed
     */
    public function addAllSettlements()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->createAllSettlements($this->charge, $this->session);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    /**
     * @return mixed
     */
    public function delAllSettlements()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->deleteAllSettlements($this->charge, $this->session);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }
}
