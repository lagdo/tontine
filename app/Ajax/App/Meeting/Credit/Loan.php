<?php

namespace App\Ajax\App\Meeting\Credit;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Loan extends CallableClass
{
    /**
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * @var LoanValidator
     */
    protected LoanValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param LoanService $loanService
     */
    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->loanService->getSession($sessionId);
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
        $loans = $this->loanService->getSessionLoans($this->session);
        $amountAvailable = $this->loanService->getAmountAvailable($this->session);

        $html = $this->view()->render('tontine.pages.meeting.loan.home')
            ->with('session', $this->session)
            ->with('loans', $loans)
            ->with('amountAvailable', $amountAvailable);
        $this->response->html('meeting-loans', $html);

        $this->jq('#btn-loans-refresh')->click($this->rq()->home());
        $this->jq('#btn-loan-add')->click($this->rq()->addLoan());
        $loanId = jq()->parent()->attr('data-loan-id')->toInt();
        $this->jq('.btn-loan-edit')->click($this->rq()->editLoan($loanId));
        $this->jq('.btn-loan-delete')->click($this->rq()->deleteLoan($loanId)
            ->confirm(trans('meeting.loan.questions.delete')));

        return $this->response;
    }

    public function addLoan()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amountAvailable = $this->loanService->getAmountAvailableValue($this->session);
        if($amountAvailable <= 0)
        {
            return $this->response;
        }

        $members = $this->loanService->getMembers();
        $title = trans('meeting.loan.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.loan.add')
            ->with('members', $members)
            ->with('amountAvailable', $amountAvailable);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createLoan(pm()->form('loan-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function createLoan(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->loanService->createLoan($this->session, $values);

        $this->dialog->hide();

        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);

        return $this->home();
    }

    public function editLoan(int $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $loan = $this->loanService->getSessionLoan($this->session, $loanId);
        $title = trans('meeting.loan.titles.edit');
        $content = $this->view()->render('tontine.pages.meeting.loan.edit')
            ->with('loan', $loan);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateLoan($loanId, pm()->form('loan-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function updateLoan(int $loanId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->loanService->updateLoan($this->session, $loanId, $values);

        $this->dialog->hide();

        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);

        return $this->home();
    }

    public function deleteLoan(int $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->loanService->deleteLoan($this->session, $loanId);

        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);

        return $this->home();
    }
}
