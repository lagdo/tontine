<?php

namespace App\Ajax\App\Meeting\Charge\Member;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Charge\Fine as Charge;
use Siak\Tontine\Service\Meeting\Charge\FineService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Charge as ChargeModel;

use function Jaxon\jq;
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

        $html = $this->view()->render('tontine.pages.meeting.charge.variable.member.home', [
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fines', $html);
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
        $onlyFined = $this->bag('meeting')->get('fine.filter', null);
        $memberCount = $this->fineService->getMemberCount($this->charge, $this->session, $onlyFined);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'meeting', 'member.page');
        $members = $this->fineService->getMembers($this->charge, $this->session, $onlyFined, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->view()->render('tontine.pages.meeting.charge.variable.member.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'members' => $members,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-charge-members', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-add-bill')->click($this->rq()->addBill($memberId));
        $this->jq('.btn-del-bill')->click($this->rq()->delBill($memberId));
        $this->jq('.btn-save-bill')->click($this->rq()->saveBill($memberId, $amount));
        $this->jq('.btn-edit-bill')->click($this->rq()->editBill($memberId));
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
    public function addBill(int $memberId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->fineService->createFine($this->charge, $this->session, $memberId);

        return $this->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function delBill(int $memberId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->fineService->deleteFine($this->charge, $this->session, $memberId);

        return $this->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function editBill(int $memberId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $bill = $this->fineService->getBill($this->charge, $this->session, $memberId);
        if($bill === null || $bill->bill->settlement !== null)
        {
            return $this->page();
        }

        $html = $this->view()->render('tontine.pages.meeting.charge.variable.member.edit', [
            'id' => $memberId,
            'amount' => $bill->bill->amount,
        ]);
        $fieldId = 'member-' . $memberId;
        $this->response->html($fieldId, $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-save-bill', "#$fieldId")->click($this->rq()->saveBill($memberId, $amount));

        return $this->response;
    }

    /**
     * @param int $memberId
     * @param int $amount
     *
     * @return mixed
     */
    public function saveBill(int $memberId, int $amount)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->fineService->deleteFine($this->charge, $this->session, $memberId);
        if($amount > 0)
        {
            $this->fineService->createFine($this->charge, $this->session, $memberId, $amount);
        }
        // $this->notify->success(trans('session.deposit.created'), trans('common.titles.success'));

        return $this->page();
    }
}
