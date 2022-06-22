<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\ChargeService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;
use App\Ajax\CallableClass;

use function jq;
use function trans;

/**
 * @databag meeting
 * @before getCharge
 */
class Fine extends CallableClass
{
    /**
     * @di
     * @var ChargeService
     */
    protected ChargeService $chargeService;

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
        $this->bag('meeting')->set('fine.filter', null);

        $html = $this->view()->render('pages.meeting.fine.home', [
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-charges', $html);
        $this->jq('#btn-fine-back')->click($this->cl(Charge::class)->rq()->home());
        $this->jq('#btn-fine-filter')->click($this->rq()->toggleFilter());

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
            $pageNumber = $this->bag('meeting')->get('fine.page', 1);
        }
        $this->bag('meeting')->set('fine.page', $pageNumber);

        $onlyFined = $this->bag('meeting')->get('fine.filter', null);
        $memberCount = $this->chargeService->getMemberCount($this->charge, $this->session, $onlyFined);
        $html = $this->view()->render('pages.meeting.fine.page', [
            'charge' => $this->charge,
            'members' => $this->chargeService->getMembers($this->charge, $this->session, $onlyFined, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $memberCount),
        ]);
        $this->response->html('meeting-charge-members', $html);

        $memberId = jq()->parent()->attr('data-member-id');
        $this->jq('.btn-add-fine')->click($this->rq()->addFine($memberId));
        $this->jq('.btn-del-fine')->click($this->rq()->delFine($memberId));
        // $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($memberId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $onlyFined = $this->bag('meeting')->get('fine.filter', null);
        // Switch between null, true and false
        $onlyFined = $onlyFined === null ? true : ($onlyFined === true ? false : null);
        $this->bag('meeting')->set('fine.filter', $onlyFined);

        return $this->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function addFine($memberId)
    {
        $this->chargeService->createFine($this->charge, $this->session, $memberId);
        // $this->notify->success(trans('session.fine.created'), trans('common.titles.success'));

        return $this->page(1);
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function delFine($memberId)
    {
        $this->chargeService->deleteFine($this->charge, $this->session, $memberId);
        // $this->notify->success(trans('session.fine.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
