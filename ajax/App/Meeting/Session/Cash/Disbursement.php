<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Stringable;

use function Jaxon\pm;
use function trans;

class Disbursement extends MeetingComponent
{
    /**
     * @var DisbursementValidator
     */
    protected DisbursementValidator $validator;

    /**
     * The constructor
     *
     * @param DisbursementService $disbursementService
     */
    public function __construct(protected DisbursementService $disbursementService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.disbursement.home', [
            'session' => $session,
            'disbursements' => $this->disbursementService->getSessionDisbursements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Balance::class)->render();
        $this->response->js('Tontine')->makeTableResponsive('content-session-disbursements');
    }

    public function addDisbursement()
    {
        $title = trans('meeting.disbursement.titles.add');
        $content = $this->renderView('pages.meeting.disbursement.add', [
            'categories' => $this->disbursementService->getCategories(),
            'members' => $this->disbursementService->getMembers(),
            'charges' => $this->disbursementService->getCharges(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createDisbursement(pm()->form('disbursement-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function createDisbursement(array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->createDisbursement($session, $values);

        $this->modal()->hide();

        $this->render();
    }

    public function editDisbursement(int $disbursementId)
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.disbursement.titles.edit');
        $content = $this->renderView('pages.meeting.disbursement.edit', [
            'disbursement' => $this->disbursementService
                ->getSessionDisbursement($session, $disbursementId),
            'categories' => $this->disbursementService->getCategories(),
            'members' => $this->disbursementService->getMembers(),
            'charges' => $this->disbursementService->getCharges(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateDisbursement($disbursementId, pm()->form('disbursement-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function updateDisbursement(int $disbursementId, array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->updateDisbursement($session, $disbursementId, $values);

        $this->modal()->hide();
        $this->render();
    }

    public function deleteDisbursement(int $disbursementId)
    {
        $session = $this->stash()->get('meeting.session');
        $this->disbursementService->deleteDisbursement($session, $disbursementId);

        $this->render();
    }
}
