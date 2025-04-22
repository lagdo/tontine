<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\FundService;
use Siak\Tontine\Validation\Meeting\LoanValidator;

use function Jaxon\pm;
use function trans;

class LoanFunc extends FuncComponent
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

    public function add()
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.loan.titles.add');
        $content = $this->renderView('pages.meeting.loan.add', [
            'amountAvailable' => $this->loanService->getAmountAvailableValue($session),
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getSessionFundList($session, false),
            'members' => $this->memberService->getMemberList(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('loan-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
        $this->response->js('Tontine')->setLoanInterestLabel();
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }
        $session = $this->stash()->get('meeting.session');
        if(!($fund = $this->fundService->getSessionFund($session, $values['fund'], false)))
        {
            $this->alert()->warning(trans('tontine.fund.errors.not_found'));
            return;
        }

        $this->loanService->createLoan($session, $member, $fund, $values);

        $this->modal()->hide();
        $this->alert()->success(trans('meeting.loan.messages.created'));
        $this->cl(Loan::class)->render();
    }

    public function edit(int $loanId)
    {
        $session = $this->stash()->get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
        if(!$loan)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }
        // A refunded loan cannot be updated.
        if($loan->refunds_count > 0)
        {
            $this->alert()->warning(trans('meeting.loan.errors.update'));
            return;
        }

        $title = trans('meeting.loan.titles.edit');
        $content = $this->renderView('pages.meeting.loan.edit', [
            'loan' => $loan,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getSessionFundList($session, false),
            'members' => $this->memberService->getMemberList(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($loanId, pm()->form('loan-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
        $this->response->js('Tontine')->setLoanInterestLabel();
    }

    /**
     * @di $validator
     */
    public function update(int $loanId, array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
        if(!$loan)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }
        // A refunded loan cannot be updated.
        if($loan->refunds_count > 0)
        {
            $this->alert()->warning(trans('meeting.loan.errors.update'));
            return;
        }

        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }
        if(!($fund = $this->fundService->getSessionFund($session, $values['fund'], false)))
        {
            $this->alert()->warning(trans('tontine.fund.errors.not_found'));
            return;
        }

        $this->loanService->updateLoan($member, $fund, $loan, $values);

        $this->modal()->hide();
        $this->alert()->success(trans('meeting.loan.messages.updated'));
        $this->cl(Loan::class)->render();
    }

    public function delete(int $loanId)
    {
        $session = $this->stash()->get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
        if(!$loan)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }
        // A refunded loan cannot be deleted.
        if($loan->refunds_count > 0)
        {
            $this->alert()->warning(trans('meeting.loan.errors.update'));
            return;
        }

        $this->loanService->deleteLoan($session, $loanId);

        $this->alert()->success(trans('meeting.loan.messages.deleted'));
        $this->cl(Loan::class)->render();
    }
}
