<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $refund = Refund::select(DB::raw('sum(debts.amount) as total'))
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('session_id', $sessionIds)
            ->value('total');
        $settlement = Settlement::select(DB::raw('sum(bills.amount) as total'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('session_id', $sessionIds)
            ->value('total');
        $debt = Debt::principal()->select(DB::raw('sum(debts.amount) as total'))
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->value('total');

        return $funding + $refund + $settlement - $debt;
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
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionLoans(Session $session): Collection
    {
        return $session->loans()->with(['member'])->get();
    }

    /**
     * Create a loan.
     *
     * @param Session $session The session
     * @param Member $member
     * @param int $principal
     * @param int $interest
     *
     * @return Loan
     */
    public function createLoan(Session $session, Member $member, int $principal, int $interest): Loan
    {
        $loan = new Loan();
        $loan->member()->associate($member);
        $loan->session()->associate($session);
        return DB::transaction(function() use($loan, $principal, $interest) {
            $loan->save();
            // Create an entry for each type of debt
            if($principal > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_PRINCIPAL, 'amount' => $principal]);
            }
            if($interest > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_INTEREST, 'amount' => $interest]);
            }
            return $loan;
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
