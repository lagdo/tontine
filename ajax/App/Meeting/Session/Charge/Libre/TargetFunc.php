<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Validation\Meeting\TargetValidator;

use function Jaxon\pm;
use function trans;
use function trim;

/**
 * @before getTarget
 */
class TargetFunc extends FuncComponent
{
    /**
     * @var TargetValidator
     */
    protected TargetValidator $validator;

    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected SettlementTargetService $targetService)
    {}

    protected function getTarget()
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        $this->stash()->set('meeting.session.charge.target', $target);
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function add()
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target !== null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderView('pages.meeting.session.charge.libre.target.add', [
            'sessions' => $this->targetService->getDeadlineSessions($session),
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

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     * @before checkChargeEdit
     * @param array $formValues
     *
     * @return mixed
     */
    public function create(array $formValues)
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target !== null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getTontineSession($values['deadline']);

        $this->targetService->createTarget($charge, $session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->modal()->hide();

        $this->stash()->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        $this->cl(Target::class)->charge($charge->id);
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function edit()
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target === null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderView('pages.meeting.session.charge.libre.target.edit', [
            'target' => $target,
            'sessions' => $this->targetService->getDeadlineSessions($session),
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

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     * @before checkChargeEdit
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function update(array $formValues)
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target === null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getTontineSession($values['deadline']);

        $this->targetService->updateTarget($target, $session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->modal()->hide();

        $this->stash()->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        $this->cl(Target::class)->charge($charge->id);
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function remove()
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target === null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $this->targetService->deleteTarget($target);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.target.messages.removed'));

        $this->stash()->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        $this->cl(Target::class)->charge($charge->id);
    }
}
