<?php

namespace App\Ajax\Web\Meeting\Session\Cash;

use App\Ajax\OpenedSessionCallable;
use App\Ajax\Web\Meeting\Session\Misc;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

class Disbursement extends OpenedSessionCallable
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

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $disbursements = $this->disbursementService->getSessionDisbursements($this->session);

        $html = $this->renderView('pages.meeting.disbursement.home', [
            'session' => $this->session,
            'disbursements' => $disbursements,
        ]);
        $this->response->html('meeting-disbursements', $html);
        $this->response->js()->makeTableResponsive('meeting-disbursements');

        $this->response->js()->showBalanceAmountsWithDelay();

        $this->response->jq('#btn-disbursements-refresh')->click($this->rq()->home());
        $this->response->jq('#btn-disbursement-add')->click($this->rq()->addDisbursement());
        $this->response->jq('#btn-disbursement-balances')
            ->click($this->rq(Misc::class)->showBalanceDetails(false));
        $disbursementId = jq()->parent()->attr('data-disbursement-id')->toInt();
        $this->response->jq('.btn-disbursement-edit')->click($this->rq()->editDisbursement($disbursementId));
        $this->response->jq('.btn-disbursement-delete')->click($this->rq()->deleteDisbursement($disbursementId)
            ->confirm(trans('meeting.disbursement.questions.delete')));

        return $this->response;
    }

    public function addDisbursement()
    {
        $title = trans('meeting.disbursement.titles.add');
        $content = $this->renderView('pages.meeting.disbursement.add')
            ->with('categories', $this->disbursementService->getCategories())
            ->with('members', $this->disbursementService->getMembers())
            ->with('charges', $this->disbursementService->getCharges());
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
     * @after showBalanceAmounts
     */
    public function createDisbursement(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->createDisbursement($this->session, $values);

        $this->dialog->hide();

        return $this->home();
    }

    public function editDisbursement(int $disbursementId)
    {
        $disbursement = $this->disbursementService->getSessionDisbursement($this->session, $disbursementId);
        $title = trans('meeting.disbursement.titles.edit');
        $content = $this->renderView('pages.meeting.disbursement.edit')
            ->with('categories', $this->disbursementService->getCategories())
            ->with('members', $this->disbursementService->getMembers())
            ->with('charges', $this->disbursementService->getCharges())
            ->with('disbursement', $disbursement);
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
     * @after showBalanceAmounts
     */
    public function updateDisbursement(int $disbursementId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->updateDisbursement($this->session, $disbursementId, $values);

        $this->dialog->hide();

        return $this->home();
    }

    /**
     * @after showBalanceAmounts
     */
    public function deleteDisbursement(int $disbursementId)
    {
        $this->disbursementService->deleteDisbursement($this->session, $disbursementId);

        return $this->home();
    }
}
