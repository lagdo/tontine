<?php

namespace App\Ajax\App\Meeting\Credit;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Refund\Interest;
use App\Ajax\App\Meeting\Refund\Principal;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag refund
 * @before getSession
 */
class Loan extends CallableClass
{
    /**
     * @di
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * @var RefundService
     */
    protected RefundService $refundService;

    /**
     * @var LoanValidator
     */
    protected LoanValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('refund')->get('session.id');
        $this->session = $this->loanService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, LoanService $loanService)
    {
        $this->session = $session;
        $this->loanService = $loanService;

        return $this->home();
    }

    public function home()
    {
        $loans = $this->loanService->getSessionLoans($this->session);
        $amountAvailable = $this->loanService->getFormattedAmountAvailable($this->session);

        $html = $this->view()->render('tontine.pages.meeting.loan.home')
            ->with('session', $this->session)
            ->with('loans', $loans)
            ->with('amountAvailable', $amountAvailable);
        $this->response->html('meeting-loans', $html);

        $this->jq('#btn-loans-refresh')->click($this->rq()->home());
        $this->jq('#btn-loan-add')->click($this->rq()->addLoan());
        $loanId = jq()->parent()->attr('data-loan-id')->toInt();
        $this->jq('.btn-loan-delete')->click($this->rq()->deleteLoan($loanId)
            ->confirm(trans('tontine.loan.questions.delete')));

        return $this->response;
    }

    public function addLoan()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amountAvailable = $this->loanService->getAmountAvailable($this->session);
        if($amountAvailable <= 0)
        {
            return $this->response;
        }

        $members = $this->loanService->getMembers();
        $title = trans('tontine.loan.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.loan.add')
            ->with('members', $members)
            ->with('amount', $amountAvailable);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveLoan(pm()->form('loan-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $validator
     * @di $refundService
     */
    public function saveLoan(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $memberId = $values['member'];
        $amount = $values['amount'];
        $interest = $values['interest'];

        if(!($member = $this->loanService->getMember($memberId)))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        $this->loanService->createLoan($this->session, $member, $amount, $interest);

        $this->dialog->hide();

        // Refresh the refunds pages
        $this->cl(Principal::class)->show($this->session, $this->refundService);
        $this->cl(Interest::class)->show($this->session, $this->refundService);

        return $this->home();
    }

    /**
     * @di $refundService
     */
    public function deleteLoan(int $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->loanService->deleteLoan($this->session, $loanId);

        // Refresh the refunds pages
        $this->cl(Principal::class)->show($this->session, $this->refundService);
        $this->cl(Interest::class)->show($this->session, $this->refundService);

        return $this->home();
    }
}
