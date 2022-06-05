<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\SettlementService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;
use App\Ajax\CallableClass;

use function jq;
use function trans;

/**
 * @databag meeting
 * @before getCharge
 */
class Settlement extends CallableClass
{
    /**
     * @di
     * @var FeeSettlementService
     */
    protected FeeSettlementService $feeService;

    /**
     * @di
     * @var FineSettlementService
     */
    protected FineSettlementService $fineService;

    /**
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
        $this->session = $this->feeService->getSession($sessionId);
        $this->charge = $this->feeService->getCharge($chargeId);

        $this->settlementService = $this->charge->is_fee ? $this->feeService : $this->fineService;
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

        $html = $this->view()->render('pages.meeting.settlement.home', [
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-charges', $html);
        $this->jq('#btn-settlements-back')->click($this->cl(Charge::class)->rq()->home());
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
        $memberCount = $this->settlementService->getMemberCount($this->charge, $this->session, $onlyUnpaid);
        $html = $this->view()->render('pages.meeting.settlement.page', [
            'charge' => $this->charge,
            'members' => $this->settlementService->getMembers($this->charge, $this->session, $onlyUnpaid, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $memberCount),
        ]);
        $this->response->html('meeting-charge-members', $html);

        $targetId = jq()->parent()->attr('data-target-id');
        $this->jq('.btn-add-settlement')->click($this->rq()->addSettlement($targetId));
        $this->jq('.btn-del-settlement')->click($this->rq()->delSettlement($targetId));
        $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($targetId));

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
     * @param int $targetId Member id for fees, bill id for fines
     *
     * @return mixed
     */
    public function addSettlement($targetId)
    {
        $this->settlementService->createSettlement($this->charge, $this->session, $targetId);
        // $this->notify->success(trans('session.settlement.created'), trans('common.titles.success'));

        return $this->page();
    }

    /**
     * @param int $targetId Member id for fees, bill id for fines
     *
     * @return mixed
     */
    public function delSettlement($targetId)
    {
        $this->settlementService->deleteSettlement($this->charge, $this->session, $targetId);
        // $this->notify->success(trans('session.settlement.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
