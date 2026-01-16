<?php

namespace Ajax\App\Meeting\Session\Credit\Loan;

use Ajax\App\Meeting\Session\FuncComponent;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Model\Loan as LoanModel;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Validation\Meeting\LoanValidator;

use function je;
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

    public function add(): void
    {
        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.loan.titles.add');
        $content = $this->renderTpl('pages.meeting.session.loan.add', [
            'amountAvailable' => $this->loanService->getAmountAvailable($session),
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getSessionFundList($session, false),
            'members' => $this->memberService->getMemberList($this->round()),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(je('loan-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
        $this->response->jo('tontine')->setLoanInterestLabel();
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($this->round(), $values['member'])))
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

        $this->cl(LoanPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    private function getSessionLoan(int $loanId): ?LoanModel
    {
        $session = $this->stash()->get('meeting.session');
        $loan = $this->loanService->getSessionLoan($session, $loanId);
        if(!$loan)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return null;
        }
        // A refunded loan cannot be updated.
        if($loan->refunds_count > 0)
        {
            $this->alert()->warning(trans('meeting.loan.errors.update'));
            return null;
        }
        return $loan;
    }

    public function edit(int $loanId): void
    {
        if(!($loan = $this->getSessionLoan($loanId)))
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.loan.titles.edit');
        $content = $this->renderTpl('pages.meeting.session.loan.edit', [
            'loan' => $loan,
            'interestTypes' => $this->loanService->getInterestTypes(),
            'funds' => $this->fundService->getSessionFundList($session, false),
            'members' => $this->memberService->getMemberList($this->round()),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($loanId, je('loan-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
        $this->response->jo('tontine')->setLoanInterestLabel();
    }

    #[Inject(attr: 'validator')]
    public function update(int $loanId, array $formValues): void
    {
        if(!($loan = $this->getSessionLoan($loanId)))
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->memberService->getMember($this->round(), $values['member'])))
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

        $this->cl(LoanPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    public function delete(int $loanId): void
    {
        if(!$this->getSessionLoan($loanId))
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->loanService->deleteLoan($session, $loanId);

        $this->alert()->success(trans('meeting.loan.messages.deleted'));

        $this->cl(LoanPage::class)->page();
        $this->cl(Balance::class)->render();
    }

    public function editDeadline(int $loanId): void
    {
        if(!($loan = $this->getSessionLoan($loanId)))
        {
            return;
        }

        $title = trans('meeting.loan.titles.deadline');
        $content = $this->renderTpl('pages.meeting.session.loan.deadline', [
            'loan' => $loan,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-danger',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.delete'),
            'class' => 'btn btn-danger',
            'click' => $this->rq()->deleteDeadline($loanId),
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateDeadline($loanId, je('loan-edit-deadline')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function updateDeadline(int $loanId, array $formValues): void
    {
        if(!($loan = $this->getSessionLoan($loanId)))
        {
            return;
        }

        $formValues['session'] = $loan->session->day_date;
        $values = $this->validator->validateDate($formValues);
        $this->loanService->updateDeadline($this->guild(), $loan, $values['deadline']);

        $this->cl(LoanPage::class)->page();
        $this->modal()->hide();
    }

    public function deleteDeadline(int $loanId): void
    {
        if(!($loan = $this->getSessionLoan($loanId)))
        {
            return;
        }

        $this->loanService->deleteDeadline($loan);

        $this->cl(LoanPage::class)->page();
        $this->modal()->hide();
    }
}
