<?php

namespace App\Ajax\App\Meeting\Charge;

use Siak\Tontine\Service\Charge\ChargeService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getCharge
 */
class Member extends CallableClass
{
    /**
     * @di
     * @var ChargeService
     */
    protected ChargeService $chargeService;

    /**
     * @di
     * @var FineService
     */
    protected FineService $fineService;

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

        $html = $this->view()->render('tontine.pages.meeting.fine.member.home', [
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fines', $html);
        $this->jq('#btn-fine-back')->click($this->cl(Fine::class)->rq()->home());
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
        $onlyFined = $this->bag('meeting')->get('fine.filter', null);
        $memberCount = $this->fineService->getMemberCount($this->charge, $this->session, $onlyFined);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'meeting', 'member.page');
        $members = $this->fineService->getMembers($this->charge, $this->session, $onlyFined, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->view()->render('tontine.pages.meeting.fine.member.page', [
            'charge' => $this->charge,
            'members' => $members,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-charge-members', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
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
    public function addFine(int $memberId)
    {
        $this->fineService->createFine($this->charge, $this->session, $memberId);

        return $this->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function delFine(int $memberId)
    {
        $this->fineService->deleteFine($this->charge, $this->session, $memberId);

        return $this->page();
    }
}
