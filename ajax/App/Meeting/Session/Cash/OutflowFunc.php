<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Session\FuncComponent;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Siak\Tontine\Validation\Meeting\OutflowValidator;

use function je;
use function trans;

class OutflowFunc extends FuncComponent
{
    /**
     * @var OutflowValidator
     */
    protected OutflowValidator $validator;

    /**
     * The constructor
     *
     * @param OutflowService $outflowService
     */
    public function __construct(protected OutflowService $outflowService)
    {}

    public function addOutflow(): void
    {
        $title = trans('meeting.outflow.titles.add');
        $content = $this->renderTpl('pages.meeting.session.outflow.add', [
            'categories' => $this->outflowService->getAccounts($this->guild()),
            'members' => $this->outflowService->getMembers($this->round()),
            'charges' => $this->outflowService->getCharges($this->round()),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createOutflow(je('outflow-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function createOutflow(array $formValues): void
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->outflowService->createOutflow($this->guild(), $session, $values);

        $this->modal()->hide();
        $this->alert()->success(trans('meeting.outflow.messages.created'));

        $this->cl(OutflowPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    public function editOutflow(int $outflowId): void
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.outflow.titles.edit');
        $content = $this->renderTpl('pages.meeting.session.outflow.edit', [
            'outflow' => $this->outflowService
                ->getSessionOutflow($session, $outflowId),
            'categories' => $this->outflowService->getAccounts($this->guild()),
            'members' => $this->outflowService->getMembers($this->round()),
            'charges' => $this->outflowService->getCharges($this->round()),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateOutflow($outflowId, je('outflow-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function updateOutflow(int $outflowId, array $formValues): void
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->outflowService->updateOutflow($this->guild(), $session, $outflowId, $values);

        $this->modal()->hide();
        $this->alert()->success(trans('meeting.outflow.messages.updated'));

        $this->cl(OutflowPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    public function deleteOutflow(int $outflowId): void
    {
        $session = $this->stash()->get('meeting.session');
        $this->outflowService->deleteOutflow($session, $outflowId);

        $this->alert()->success(trans('meeting.outflow.messages.deleted'));

        $this->cl(OutflowPage::class)->page();
        $this->cl(Balance::class)->render();
    }
}
