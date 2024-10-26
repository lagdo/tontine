<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\Cache;
use App\Ajax\MeetingComponent;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Meeting\LoanValidator;

use function Jaxon\pm;
use function trans;

class Loan extends MeetingComponent
{
    /**
     * @var LoanValidator
     */
    protected LoanValidator $validator;

    /**
     * The constructor
     *
     * @param LoanService $loanService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(protected LoanService $loanService,
        protected FundService $fundService, protected MemberService $memberService)
    {}

    public function html(): string
    {
        $session = Cache::get('meeting.session');
        $loans = $this->loanService->getSessionLoans($session);

        return (string)$this->renderView('pages.meeting.loan.home', [
            'session' => $session,
            'loans' => $loans,
            'defaultFund' => $this->fundService->getDefaultFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-loans');
        $this->response->js()->showBalanceAmountsWithDelay();
    }

    public function addLoan()
    {
        $session = Cache::get('meeting.session');
        $title = trans('meeting.loan.titles.add');
        $content = $this->renderView('pages.meeting.loan.add', [
            'amountAvailable' => $this->loanService->getAmountAvailableValue($session),
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
        $this->response->js()->setLoanInterestLabel();

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function createLoan(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }
        if(!($fund = $this->fundService->getFund($values['fund'], true, true)))
        {
            $this->notify->warning(trans('tontine.fund.errors.not_found'));
            return $this->response;
        }

        $session = Cache::get('meeting.session');
        $this->loanService->createLoan($session, $member, $fund, $values);

        $this->dialog->hide();

        return $this->render();
    }

    public function editLoan(int $loanId)
    {
        $session = Cache::get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
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
        $content = $this->renderView('pages.meeting.loan.edit', [
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
        $this->response->js()->setLoanInterestLabel();

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function updateLoan(int $loanId, array $formValues)
    {
        $session = Cache::get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
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
        if(!($fund = $this->fundService->getFund($values['fund'], true, true)))
        {
            $this->notify->warning(trans('tontine.fund.errors.not_found'));
            return $this->response;
        }

        $this->loanService->updateLoan($member, $fund, $loan, $values);

        $this->dialog->hide();

        return $this->render();
    }

    public function deleteLoan(int $loanId)
    {
        $session = Cache::get('meeting.session');
        $this->loanService->deleteLoan($session, $loanId);

        return $this->render();
    }
}
