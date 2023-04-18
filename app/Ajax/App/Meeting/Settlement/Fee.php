<?php

namespace App\Ajax\App\Meeting\Settlement;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Charge\Fee as Charge;
use Siak\Tontine\Service\Charge\FeeBillService;
use Siak\Tontine\Service\Charge\SettlementService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getCharge
 */
class Fee extends CallableClass
{
    /**
     * @di
     * @var FeeBillService
     */
    protected FeeBillService $billService;

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
            $this->target()->args()[0] : $this->bag('meeting')->get('charge.id');
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
        $this->bag('meeting')->set('charge.id', $chargeId);
        $this->bag('meeting')->set('settlement.filter', null);

        $html = $this->view()->render('tontine.pages.meeting.settlement.home', [
            'charge' => $this->charge,
            'type' => 'fee',
        ]);
        $this->response->html('meeting-fees', $html);
        $this->jq('#btn-fee-settlements-back')->click($this->cl(Charge::class)->rq()->home());
        $this->jq('#btn-fee-settlements-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.filter', null);
        $billCount = $this->billService->getBillCount($this->charge, $this->session, $onlyUnpaid);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $billCount, 'meeting', 'settlement.page');
        $bills = $this->billService->getBills($this->charge, $this->session, $onlyUnpaid, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $billCount);

        $html = $this->view()->render('tontine.pages.meeting.settlement.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'bills' => $bills,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-bills', $html);

        $billId = jq()->parent()->attr('data-bill-id')->toInt();
        $this->jq('.btn-add-settlement')->click($this->rq()->addSettlement($billId));
        $this->jq('.btn-del-settlement')->click($this->rq()->delSettlement($billId));
        $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($billId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.filter', $onlyUnpaid);

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
        // $this->notify->success(trans('session.settlement.created'), trans('common.titles.success'));

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
        // $this->notify->success(trans('session.settlement.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
