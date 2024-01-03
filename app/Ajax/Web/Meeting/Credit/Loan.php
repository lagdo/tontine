<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Session\Session;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Meeting\LoanValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

class Loan extends CallableSessionClass
{
    /**
     * @var FundService
     */
    protected FundService $fundService;

    /**
     * @var LoanValidator
     */
    protected LoanValidator $validator;

    /**
     * The constructor
     *
     * @param LoanService $loanService
     * @param MemberService $memberService
     */
    public function __construct(protected LoanService $loanService,
        protected MemberService $memberService)
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
        $loans = $this->loanService->getSessionLoans($this->session);

        $html = $this->render('pages.meeting.loan.home', [
            'session' => $this->session,
            'loans' => $loans,
        ]);
        $this->response->html('meeting-loans', $html);

        $this->jq('#btn-loans-refresh')->click($this->rq()->home());
        $this->jq('#btn-loan-add')->click($this->rq()->addLoan());
        $this->jq('#btn-loan-balances')
            ->click($this->cl(Session::class)->rq()->showBalanceDetails(true));
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
        $title = trans('meeting.loan.titles.add');
        $content = $this->render('pages.meeting.loan.add', [
            'amountAvailable' => $amountAvailable,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getFundList(),
            'members' => $this->memberService->getMemberList(),
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
     * @after showBalanceAmounts
     */
    public function createLoan(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->loanService->createLoan($member, $this->session, $values);

        $this->dialog->hide();

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
        // A refunded loan cannot be updated.
        if($loan->refunds_count > 0)
        {
            $this->notify->warning(trans('meeting.loan.errors.update'));
            return $this->response;
        }

        $title = trans('meeting.loan.titles.edit');
        $content = $this->render('pages.meeting.loan.edit', [
            'loan' => $loan,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getFundList(),
            'members' => $this->memberService->getMemberList(),
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
     * @after showBalanceAmounts
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
        // A refunded loan cannot be updated.
        if($loan->refunds_count > 0)
        {
            $this->notify->warning(trans('meeting.loan.errors.update'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->loanService->updateLoan($member, $loan, $values);

        $this->dialog->hide();

        return $this->home();
    }

    /**
     * @after showBalanceAmounts
     */
    public function deleteLoan(int $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->loanService->deleteLoan($this->session, $loanId);

        return $this->home();
    }
}
