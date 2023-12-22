<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
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
     * @var FundService
     */
    protected FundService $fundService;

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
    public function refreshAmount(SessionModel $session)
    {
        $amount = $this->loanService->getFormattedAmountAvailable($session);
        $html = trans('meeting.loan.labels.amount_available', ['amount' => $amount]);
        $this->response->html('loan_amount_available', $html);
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

        $html = $this->render('pages.meeting.loan.home')
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

    /**
     * @di $fundService
     */
    public function addLoan()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amountAvailable = $this->loanService->getAmountAvailableValue($this->session);
        $members = $this->loanService->getMembers();
        $title = trans('meeting.loan.titles.add');
        $content = $this->render('pages.meeting.loan.add', [
            'members' => $members,
            'amountAvailable' => $amountAvailable,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getFundList(),
        ]);
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
        $this->response->script('setLoanInterestLabel()');

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
        // Refresh the amounts available
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    /**
     * @di $fundService
     */
    public function editLoan(int $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $loan = $this->loanService->getSessionLoan($this->session, $loanId);
        if(!$loan)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }
        // A refunded loan, or that was created from a remitment cannot be updated.
        if($loan->refunds_count > 0 || $loan->remitment_id)
        {
            $this->notify->warning(trans('meeting.loan.errors.update'));
            return $this->response;
        }

        $title = trans('meeting.loan.titles.edit');
        $content = $this->render('pages.meeting.loan.edit', [
            'loan' => $loan,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getFundList(),
        ]);
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
        $this->response->script('setLoanInterestLabel()');

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
        $loan = $this->loanService->getSessionLoan($this->session, $loanId);
        if(!$loan)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }
        // A refunded loan, or that was created from a remitment cannot be updated.
        if($loan->refunds_count > 0 || $loan->remitment_id)
        {
            $this->notify->warning(trans('meeting.loan.errors.update'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->loanService->updateLoan($this->session, $loan, $values);

        $this->dialog->hide();
        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);
        // Refresh the amounts available
        $this->cl(Disbursement::class)->refreshAmount($this->session);

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
        // Refresh the amounts available
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }
}
