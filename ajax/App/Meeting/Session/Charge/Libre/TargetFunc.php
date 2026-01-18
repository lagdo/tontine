<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Validation\Meeting\TargetValidator;

use function Jaxon\form;
use function trans;

#[Before('getTarget')]
class TargetFunc extends FuncComponent
{
    use ChargeTrait;

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

    protected function getTarget(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        $this->stash()->set('meeting.session.charge.target', $target);
    }

    /**
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function add(): void
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target !== null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderTpl('pages.meeting.session.charge.libre.target.add', [
            'sessions' => $this->targetService->getDeadlineSessions($session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(form('target-form')),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param array $formValues
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
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
        $this->targetService->createTarget($charge, $session, $values);

        $this->modal()->hide();

        $this->stash()->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        $this->cl(Target::class)->charge($charge->id);
    }

    /**
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function edit(): void
    {
        $target = $this->stash()->get('meeting.session.charge.target');
        if($target === null)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderTpl('pages.meeting.session.charge.libre.target.edit', [
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
            'click' => $this->rq()->update(form('target-form')),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    #[Inject(attr: 'validator')]
    public function update(array $formValues): void
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
        $this->targetService->updateTarget($target, $session, $values);

        $this->modal()->hide();

        $this->stash()->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        $this->cl(Target::class)->charge($charge->id);
    }

    /**
     * @return mixed
     */
    #[Before('checkChargeEdit')]
    public function remove(): void
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
