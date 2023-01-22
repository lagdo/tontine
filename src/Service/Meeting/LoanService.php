<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use stdClass;

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
        $loans = $session->loans()->with('member');
        if($page > 0 )
        {
            $loans->take($this->tenantService->getLimit());
            $loans->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $loans->get();
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
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');

        // The amount available for lending is the sum of the refunds,
        // minus the sum of the loans, for all the sessions before the selected.
        $loan = Loan::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total');
        $interest = Refund::interest()
            ->join('loans', 'refunds.loan_id', '=', 'loans.id')
            ->select(DB::raw('sum(loans.interest) as total'))
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total');
        $principal = Refund::principal()
            ->join('loans', 'refunds.loan_id', '=', 'loans.id')
            ->select(DB::raw('sum(loans.amount) as total'))
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total');

        return $principal + $interest - $loan;
    }

    /**
     * @param Session $session    The session
     *
     * @return string
     */
    public function getFormattedAmountAvailable(Session $session): string
    {
        return $this->localeService->formatMoney($this->getAmountAvailable($session));
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
        $loans = $session->loans()->with(['member'])->get();
        $loans->each(function($loan) {
            $loan->amount = $this->localeService->formatMoney($loan->amount);
            $loan->interest = $this->localeService->formatMoney($loan->interest);
        });
        return $loans;
    }

    /**
     * Create a loan.
     *
     * @param Session $session The session
     * @param int $memberId
     * @param int $amount
     * @param int $interest
     *
     * @return void
     */
    public function createLoan(Session $session, int $memberId, int $amount, int $interest): void
    {
        $member = $this->getMember($memberId);
        if(!$member)
        {
            return; // Todo: throw an exception.
        }

        $loan = new Loan();
        $loan->amount = $amount;
        $loan->interest = $interest;
        $loan->member()->associate($member);
        $loan->session()->associate($session);
        $loan->save();
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
        $session->loans()->where('id', $loanId)->delete();
    }
}
