<?php

namespace App\Ajax\Web\Meeting\Session\Cash;

use App\Ajax\Cache;
use App\Ajax\MeetingComponent;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;

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

    public function html(): string
    {
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.disbursement.home', [
            'session' => $session,
            'disbursements' => $this->disbursementService->getSessionDisbursements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-disbursements');
        $this->response->js()->showBalanceAmountsWithDelay();
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function createDisbursement(array $formValues)
    {
        $session = Cache::get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->createDisbursement($session, $values);

        $this->dialog->hide();

        return $this->render();
    }

    public function editDisbursement(int $disbursementId)
    {
        $session = Cache::get('meeting.session');
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function updateDisbursement(int $disbursementId, array $formValues)
    {
        $session = Cache::get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->updateDisbursement($session, $disbursementId, $values);

        $this->dialog->hide();

        return $this->render();
    }

    public function deleteDisbursement(int $disbursementId)
    {
        $session = Cache::get('meeting.session');
        $this->disbursementService->deleteDisbursement($session, $disbursementId);

        return $this->render();
    }
}
