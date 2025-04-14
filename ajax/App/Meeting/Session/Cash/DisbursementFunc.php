<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;

use function Jaxon\pm;
use function trans;

class DisbursementFunc extends FuncComponent
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

    public function addDisbursement()
    {
        $title = trans('meeting.disbursement.titles.add');
        $content = $this->renderView('pages.meeting.disbursement.add', [
            'categories' => $this->disbursementService->getAccounts(),
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

        $this->cl(Disbursement::class)->render();
    }

    public function editDisbursement(int $disbursementId)
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.disbursement.titles.edit');
        $content = $this->renderView('pages.meeting.disbursement.edit', [
            'disbursement' => $this->disbursementService
                ->getSessionDisbursement($session, $disbursementId),
            'categories' => $this->disbursementService->getAccounts(),
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
        $this->cl(Disbursement::class)->render();
    }

    public function deleteDisbursement(int $disbursementId)
    {
        $session = $this->stash()->get('meeting.session');
        $this->disbursementService->deleteDisbursement($session, $disbursementId);

        $this->cl(Disbursement::class)->render();
    }
}
