<?php

namespace App\Ajax\Web\Meeting\Charge\Libre;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Charge\LibreFee as Charge;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Model\SettlementTarget as TargetModel;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Validation\Meeting\TargetValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;
use function trim;

/**
 * @before getTarget
 */
class Target extends CallableSessionClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TargetValidator
     */
    protected TargetValidator $validator;

    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

    /**
     * @var TargetModel|null
     */
    protected ?TargetModel $target = null;

    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     * @param ChargeService $chargeService
     * @param SessionService $sessionService
     */
    public function __construct(protected SettlementTargetService $targetService,
        protected ChargeService $chargeService, SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    protected function getTarget()
    {
        $chargeId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('charge.id');
        $this->charge = $this->chargeService->getCharge($chargeId);
        if($this->session !== null && $this->charge !== null)
        {
            $this->target = $this->targetService->getTarget($this->charge, $this->session);
        }
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('charge.id', $chargeId);

        $html = $this->render('pages.meeting.charge.libre.target.home', [
            'charge' => $this->charge,
            'target' => $this->target,
        ]);
        $this->response->html('meeting-fees-libre', $html);
        $this->jq('#btn-fee-target-add')->click($this->rq()->add());
        $this->jq('#btn-fee-target-edit')->click($this->rq()->edit());
        $this->jq('#btn-fee-target-remove')->click($this->rq()->remove()
            ->confirm(trans('meeting.target.questions.remove')));
        $this->jq('#btn-fee-target-back')->click($this->cl(Charge::class)->rq()->home());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        if($this->target === null)
        {
            return $this->response;
        }

        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $memberCount = $this->targetService->getMemberCount($search);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'meeting', 'target.page');
        $members = $this->targetService->getMembersWithSettlements($this->charge,
            $this->target, $search, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->render('pages.meeting.charge.libre.target.page', [
            'search' => $search,
            'session' => $this->session,
            'target' => $this->target,
            'charge' => $this->charge,
            'members' => $members,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-libre-target', $html);
        $this->jq('#btn-fee-libre-search')
            ->click($this->rq()->search(jq('#txt-fee-member-search')->val()));

        return $this->response;
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        return $this->page();
    }

    /**
     * @return mixed
     */
    public function add()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if($this->session === null || $this->charge === null)
        {
            return $this->response;
        }
        if($this->target !== null)
        {
            return $this->response;
        }

        $title = trans('meeting.target.titles.set');
        $content = $this->render('pages.meeting.charge.libre.target.add', [
            'sessions' => $this->targetService->getDeadlineSessions($this->session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('target-form')),
        ]];

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @param array $formValues
     *
     * @return mixed
     */
    public function create(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if($this->session === null || $this->charge === null)
        {
            return $this->response;
        }
        if($this->target !== null)
        {
            return $this->response;
        }

        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getSession($values['deadline']);

        $this->targetService->createTarget($this->charge, $this->session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->dialog->hide();

        $this->target = $this->targetService->getTarget($this->charge, $this->session);
        return $this->home($this->charge->id);
    }

    /**
     * @return mixed
     */
    public function edit()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if($this->target === null)
        {
            return $this->response;
        }

        $title = trans('meeting.target.titles.set');
        $content = $this->render('pages.meeting.charge.libre.target.edit', [
            'target' => $this->target,
            'sessions' => $this->targetService->getDeadlineSessions($this->session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update(pm()->form('target-form')),
        ]];

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function update(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if($this->target === null)
        {
            return $this->response;
        }

        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getSession($values['deadline']);

        $this->targetService->updateTarget($this->target, $this->session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->dialog->hide();

        $this->target = $this->targetService->getTarget($this->charge, $this->session);
        return $this->home($this->charge->id);
    }

    /**
     * @return mixed
     */
    public function remove()
    {
        if($this->session === null || $this->charge === null)
        {
            return $this->response;
        }
        if($this->target === null)
        {
            return $this->response;
        }

        $this->targetService->deleteTarget($this->target);
        $this->notify->success(trans('meeting.target.messages.removed'), trans('common.titles.success'));

        $this->target = $this->targetService->getTarget($this->charge, $this->session);
        return $this->home($this->charge->id);
    }
}
