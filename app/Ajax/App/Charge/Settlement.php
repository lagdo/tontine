<?php

namespace App\Ajax\App\Charge;

use Siak\Tontine\Service\Charge\ChargeService;
use Siak\Tontine\Service\Charge\SettlementService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getCharge
 */
class Settlement extends CallableClass
{
    /**
     * @di
     * @var ChargeService
     */
    protected ChargeService $chargeService;

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
        $this->session = $this->chargeService->getSession($sessionId);
        $this->charge = $this->chargeService->getCharge($chargeId);
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
        ]);
        if($this->charge->is_fee)
        {
            $this->response->html('meeting-fees', $html);
            $this->jq('#btn-settlements-back')->click($this->cl(Fee::class)->rq()->home());
        }
        if($this->charge->is_fine)
        {
            $this->response->html('meeting-fines', $html);
            $this->jq('#btn-settlements-back')->click($this->cl(Fine::class)->rq()->home());
        }
        $this->jq('#btn-settlements-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('meeting')->get('settlement.page', 1);
        }
        $this->bag('meeting')->set('settlement.page', $pageNumber);

        $onlyUnpaid = $this->bag('meeting')->get('settlement.filter', null);
        $billCount = $this->settlementService->getBillCount($this->charge, $this->session, $onlyUnpaid);
        $html = $this->view()->render('tontine.pages.meeting.settlement.page', [
            'charge' => $this->charge,
            'bills' => $this->settlementService->getBills($this->charge, $this->session, $onlyUnpaid, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $billCount),
        ]);
        $this->response->html('meeting-charge-bills', $html);

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
        $this->settlementService->deleteSettlement($this->charge, $this->session, $billId);
        // $this->notify->success(trans('session.settlement.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
