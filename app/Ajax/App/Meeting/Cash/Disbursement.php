<?php

namespace App\Ajax\App\Meeting\Cash;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Credit\Loan;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Disbursement extends CallableClass
{
    /**
     * @var DisbursementService
     */
    protected DisbursementService $disbursementService;

    /**
     * @var DisbursementValidator
     */
    protected DisbursementValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param DisbursementService $disbursementService
     */
    public function __construct(DisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->disbursementService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function refreshAmount(SessionModel $session)
    {
        $amount = $this->disbursementService->getAmountAvailableValue($session);
        $html = trans('meeting.disbursement.labels.amount_available', ['amount' => $amount]);
        $this->response->html('total_amount_available', $html);
    }

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
        $amountAvailable = $this->disbursementService->getAmountAvailable($this->session);

        $html = $this->view()->render('tontine.pages.meeting.disbursement.home')
            ->with('session', $this->session)
            ->with('disbursements', $disbursements)
            ->with('amountAvailable', $amountAvailable);
        $this->response->html('meeting-disbursements', $html);

        $this->jq('#btn-disbursements-refresh')->click($this->rq()->home());
        $this->jq('#btn-disbursement-add')->click($this->rq()->addDisbursement());
        $disbursementId = jq()->parent()->attr('data-disbursement-id')->toInt();
        $this->jq('.btn-disbursement-edit')->click($this->rq()->editDisbursement($disbursementId));
        $this->jq('.btn-disbursement-delete')->click($this->rq()->deleteDisbursement($disbursementId)
            ->confirm(trans('meeting.disbursement.questions.delete')));

        return $this->response;
    }

    public function addDisbursement()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $title = trans('meeting.disbursement.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.disbursement.add')
            ->with('categories', $this->disbursementService->getCategories())
            ->with('members', $this->disbursementService->getMembers());
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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->createDisbursement($this->session, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function editDisbursement(int $disbursementId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $disbursement = $this->disbursementService->getSessionDisbursement($this->session, $disbursementId);
        $title = trans('meeting.disbursement.titles.edit');
        $content = $this->view()->render('tontine.pages.meeting.disbursement.edit')
            ->with('categories', $this->disbursementService->getCategories())
            ->with('members', $this->disbursementService->getMembers())
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
     */
    public function updateDisbursement(int $disbursementId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->disbursementService->updateDisbursement($this->session, $disbursementId, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function deleteDisbursement(int $disbursementId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->disbursementService->deleteDisbursement($this->session, $disbursementId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);

        return $this->home();
    }
}
