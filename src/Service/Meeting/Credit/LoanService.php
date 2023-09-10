<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;

class LoanService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(int $poolId): ?Pool
    {
        return $this->tenantService->round()->pools()->find($poolId);
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        return $this->tenantService->tontine()->members()->find($memberId);
    }

    /**
     * @return array
     */
    public function getInterestTypes(): array
    {
        return [
            Loan::INTEREST_FIXED => trans('meeting.loan.interest.f'),
            Loan::INTEREST_SIMPLE => trans('meeting.loan.interest.s'),
            Loan::INTEREST_COMPOUND => trans('meeting.loan.interest.c'),
        ];
    }

    /**
     * Get the loans for a given session.
     *
     * @param Session $session    The session
     * @param int $page
     *
     * @return Collection
     */
    public function getLoans(Session $session, int $page = 0): Collection
    {
        return $session->loans()->with(['member', 'principal_debt', 'interest_debt'])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getAmountAvailable(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        // The amount available for lending is the sum of the fundings and refunds,
        // minus the sum of the loans, for all the sessions until the selected.
        $funding = Funding::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total');
        $settlement = Settlement::select(DB::raw('sum(bills.amount) as total'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->where('bills.lendable', true)
            ->value('total');
        $refund = Refund::select(DB::raw('sum(debts.amount) as total'))
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total');
        $debt = Debt::principal()->select(DB::raw('sum(debts.amount) as total'))
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->value('total');
        $disbursement = Disbursement::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->where('charge_lendable', true)
            ->value('total');

        return $funding + $settlement + $refund - $debt - $disbursement;
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return float
     */
    public function getAmountAvailableValue(Session $session): float
    {
        return $this->localeService->getMoneyValue($this->getAmountAvailable($session));
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return string
     */
    public function getFormattedAmountAvailable(Session $session): string
    {
        return $this->localeService->formatMoney($this->getAmountAvailable($session));
    }

    /**
     * Get the loans for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionLoans(Session $session): Collection
    {
        return $session->loans()->whereDoesntHave('remitment')->with(['member'])->get();
    }

    /**
     * Get a loan for a given session.
     *
     * @param Session $session
     * @param int $loanId
     *
     * @return Loan|null
     */
    public function getSessionLoan(Session $session, int $loanId): ?Loan
    {
        return $session->loans()->with(['member'])->withCount('refunds')->find($loanId);
    }

    /**
     * Create a loan.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createLoan(Session $session, array $values): void
    {
        $member = $this->getMember($values['member']);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        $loan = new Loan();
        $loan->interest_type = $values['interest_type'];
        $loan->interest_rate = $values['interest_rate'];
        $loan->member()->associate($member);
        $loan->session()->associate($session);
        DB::transaction(function() use($loan, $values) {
            $loan->save();

            $principal = $values['principal'];
            $interest = $values['interest'];
            // Create an entry for each type of debt
            if($principal > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_PRINCIPAL, 'amount' => $principal]);
            }
            if($interest > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_INTEREST, 'amount' => $interest]);
            }
        });
    }

    /**
     * Update a loan.
     *
     * @param Session $session The session
     * @param Loan $loan
     * @param array $values
     *
     * @return void
     */
    public function updateLoan(Session $session, Loan $loan, array $values): void
    {
        $loan->interest_type = $values['interest_type'];
        $loan->interest_rate = $values['interest_rate'];
        DB::transaction(function() use($loan, $values) {
            $loan->save();

            $principal = $values['principal'];
            $interest = $values['interest'];
            $loan->debts()->principal()->update(['amount' => $principal]);
            // The interest debt may need to be created or deleted.
            if($interest <= 0)
            {
                $loan->debts()->where('type', Debt::TYPE_INTEREST)->delete();
                return;
            }
            $loan->debts()->updateOrCreate(['type' => Debt::TYPE_INTEREST], ['amount' => $interest]);
        });
    }

    /**
     * Delete a loan.
     *
     * @param Session $session The session
     * @param int $loanId
     *
     * @return void
     */
    public function deleteLoan(Session $session, int $loanId): void
    {
        if(($loan = $session->loans()->find($loanId)) !== null)
        {
            DB::transaction(function() use($loan) {
                $loan->refunds()->delete();
                $loan->debts()->delete();
                $loan->delete();
            });
        }
    }
}
