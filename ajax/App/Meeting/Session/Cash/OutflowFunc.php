<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Siak\Tontine\Validation\Meeting\OutflowValidator;

use function Jaxon\pm;
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

    public function addOutflow()
    {
        $title = trans('meeting.outflow.titles.add');
        $content = $this->renderView('pages.meeting.outflow.add', [
            'categories' => $this->outflowService->getAccounts(),
            'members' => $this->outflowService->getMembers(),
            'charges' => $this->outflowService->getCharges(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createOutflow(pm()->form('outflow-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function createOutflow(array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->outflowService->createOutflow($session, $values);

        $this->modal()->hide();

        $this->cl(Outflow::class)->render();
    }

    public function editOutflow(int $outflowId)
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.outflow.titles.edit');
        $content = $this->renderView('pages.meeting.outflow.edit', [
            'outflow' => $this->outflowService
                ->getSessionOutflow($session, $outflowId),
            'categories' => $this->outflowService->getAccounts(),
            'members' => $this->outflowService->getMembers(),
            'charges' => $this->outflowService->getCharges(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateOutflow($outflowId, pm()->form('outflow-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function updateOutflow(int $outflowId, array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->outflowService->updateOutflow($session, $outflowId, $values);

        $this->modal()->hide();
        $this->cl(Outflow::class)->render();
    }

    public function deleteOutflow(int $outflowId)
    {
        $session = $this->stash()->get('meeting.session');
        $this->outflowService->deleteOutflow($session, $outflowId);

        $this->cl(Outflow::class)->render();
    }
}
