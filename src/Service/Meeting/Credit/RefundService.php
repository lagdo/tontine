<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function pow;
use function trans;

class RefundService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     */
    public function __construct(TenantService $tenantService, LocaleService $localeService)
    {
        $this->tenantService = $tenantService;
        $this->localeService = $localeService;
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
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $typeDebt
     * @param bool $onlyPaid
     *
     * @return Builder
     */
    private function getQuery(int $sessionId, Collection $prevSessions,
        bool $typeDebt, ?bool $onlyPaid): Builder
    {
        return Debt::when($onlyPaid === false, function($query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function($query) {
                return $query->whereHas('refund');
            })
            ->where(function($query) use($typeDebt, $sessionId, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function($query) use($typeDebt, $sessionId) {
                    $query->whereHas('loan', function(Builder $query) use($typeDebt, $sessionId) {
                        $query->where('session_id', $sessionId)
                            ->when($typeDebt, function($query) {
                                return $query->whereNull('remitment_id');
                            })->when(!$typeDebt, function($query) {
                                return $query->whereNotNull('remitment_id');
                            });
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function($query) use($typeDebt, $sessionId, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($typeDebt, $prevSessions) {
                        $query->whereIn('session_id', $prevSessions)
                            ->when($typeDebt, function($query) {
                                return $query->whereNull('remitment_id');
                            })->when(!$typeDebt, function($query) {
                                return $query->whereNotNull('remitment_id');
                            });
                    })
                    ->where(function($query) use($sessionId) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', function(Builder $query) use($sessionId) {
                            $query->where('session_id', $sessionId);
                        });
                    });
                });
            });
    }

    /**
     * Get the number of debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getDebtCount(Session $session, ?bool $onlyPaid): int
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        return $this->getQuery($session->id, $prevSessions, true, $onlyPaid)->count();
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getDebts(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');

        return $this->getQuery($session->id, $prevSessions, true, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund', 'partial_refunds.session'])
            ->get()
            ->sortBy('loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Get the number of auctions.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getAuctionCount(Session $session, ?bool $onlyPaid): int
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        return $this->getQuery($session->id, $prevSessions, false, $onlyPaid)->count();
    }

    /**
     * Get the auctions.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getAuctions(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');

        return $this->getQuery($session->id, $prevSessions, false, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund'])
            ->get()
            ->sortBy('loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Count the sessions.
     *
     * @param Session $fromSession The session to start from
     * @param Session $toSession The session to end to
     *
     * @return int
     */
    private function getSessionCount(Session $fromSession, Session $toSession): int
    {
        // Count the sessions.
        return $this->tenantService->round()->sessions
            ->filter(function($session) use($fromSession, $toSession) {
                return $session->start_at > $fromSession->start_at &&
                    $session->start_at <= $toSession->start_at;
            })
            ->count();
    }

    /**
     * Get the last session for interest calculation
     *
     * @param Session $currentSession
     * @param Debt $principalDebt
     *
     * @return Session
     */
    private function getLastSession(Session $currentSession, Debt $principalDebt): Session
    {
        return $principalDebt->refund &&
            $principalDebt->refund->session->start_at < $currentSession->start_at ?
            $principalDebt->refund->session : $currentSession;
    }

    /**
     * Get the simple interest amount.
     *
     * @param Session $currentSession
     * @param Debt $debt
     *
     * @return int
     */
    private function getSimpleInterestAmount(Session $currentSession, Debt $debt): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        $interestRate = $debt->loan->interest_rate / 10000;

        $interestAmount = 0;
        $fromSession = $debt->loan->session;
        // Take refunds before the current session and filter by session date.
        $partialRefunds = $principalDebt->partial_refunds
            ->filter(function($refund) use($currentSession) {
                return $refund->session->start_at < $currentSession->start_at;
            })
            ->sortBy('session.start_at');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($fromSession, $refund->session);
            $interestAmount += (int)($loanAmount * $interestRate * $sessionCount);
            // For the next loop
            $loanAmount -= $refund->amount;
            $fromSession = $refund->session;
        }

        $lastSession = $this->getLastSession($currentSession, $principalDebt);
        $sessionCount = $this->getSessionCount($fromSession, $lastSession);

        return $interestAmount + (int)($loanAmount * $interestRate * $sessionCount);
    }

    /**
     * Get the compound interest amount.
     *
     * @param Session $currentSession
     * @param Debt $debt
     *
     * @return int
     */
    private function getCompoundInterestAmount(Session $currentSession, Debt $debt): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        $interestRate = $debt->loan->interest_rate / 10000;

        $interestAmount = 0;
        $fromSession = $debt->loan->session;
        // Take refunds before the current session and filter by session date.
        $partialRefunds = $principalDebt->partial_refunds
            ->filter(function($refund) use($currentSession) {
                return $refund->session->start_at < $currentSession->start_at;
            })
            ->sortBy('session.start_at');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($fromSession, $refund->session);
            $interestAmount += (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
            // For the next loop
            $loanAmount -= $refund->amount - $interestAmount;
            $fromSession = $refund->session;
        }

        $lastSession = $this->getLastSession($currentSession, $principalDebt);
        $sessionCount = $this->getSessionCount($fromSession, $lastSession);

        return $interestAmount + (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
    }

    /**
     * Get the amount of a given debt.
     *
     * @param Session $session The session
     * @param Debt $debt
     *
     * @return int
     */
    public function getDebtAmount(Session $currentSession, Debt $debt): int
    {
        if($debt->is_principal || $debt->refund || $debt->loan->fixed_interest)
        {
            return $debt->amount;
        }
        return $debt->loan->simple_interest ?
            $this->getSimpleInterestAmount($currentSession, $debt) :
            $this->getCompoundInterestAmount($currentSession, $debt);
    }

    /**
     * Get the refunds for a given session.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getRefunds(Session $session, int $page = 0): Collection
    {
        return $session->refunds()
            ->page($page, $this->tenantService->getLimit())
            ->with('debt.loan.member')
            ->get();
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function createRefund(Session $session, int $debtId): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $debt = Debt::whereHas('loan', function(Builder $query) use($sessionIds) {
            $query->whereIn('session_id', $sessionIds);
        })->with(['partial_refunds'])->find($debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('tontine.loan.errors.not_found'));
        }

        $refund = new Refund();
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        DB::transaction(function() use($session, $debt, $refund) {
            $refund->save();
            // For simple or compound interest, also save the final amount.
            if($debt->is_interest && ($debt->loan->simple_interest || $debt->loan->compound_interest))
            {
                $debt->amount = $debt->loan->simple_interest ?
                    $this->getSimpleInterestAmount($session, $debt) :
                    $this->getCompoundInterestAmount($session, $debt);
                $debt->save();
            }
        });
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $debtId): void
    {
        $refund = Refund::where('session_id', $session->id)->where('debt_id', $debtId)->first();
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        if(($refund->online))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $refund->delete();
    }

    /**
     * Get the number of partial refunds.
     *
     * @param Session $session The session
     *
     * @return int
     */
    public function getPartialRefundCount(Session $session): int
    {
        return $session->partial_refunds()->count();
    }

    /**
     * Get the partial refunds.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getPartialRefunds(Session $session, int $page = 0): Collection
    {
        return $session->partial_refunds()
            ->page($page, $this->tenantService->getLimit())
            ->with(['debt.refund', 'debt.loan.member', 'debt.loan.session'])
            ->get()
            ->sortBy('debt.loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Get debt list for dropdown.
     *
     * @param Session $session The session
     *
     * @return Collection
     */
    public function getUnpaidDebtList(Session $session): Collection
    {
        return $this->getDebts($session, false)
            ->filter(function($debt) use($session) {
                return $this->getDebtAmount($session, $debt) > 0;
            })
            ->keyBy('id')
            ->map(function($debt) use($session) {
                $amount = $this->getDebtAmount($session, $debt) -
                    $debt->partial_refunds->sum('amount');
                return $debt->loan->member->name . ' - ' . $debt->loan->session->title .
                    ' - ' . trans('meeting.loan.labels.' . $debt->type) .
                    ' - ' . $this->localeService->formatMoney($amount, true);
            });
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     * @param int $amount
     *
     * @return void
     */
    public function createPartialRefund(Session $session, int $debtId, int $amount): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $debt = Debt::whereHas('loan', function(Builder $query) use($sessionIds) {
            $query->whereIn('session_id', $sessionIds);
        })->find($debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        // A partial refund must not totally refund a debt
        if($amount >= $debt->due_amount)
        {
            throw new MessageException(trans('meeting.refund.errors.pr_amount'));
        }

        $refund = new PartialRefund();
        $refund->amount = $amount;
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        $refund->save();
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $refundId
     *
     * @return void
     */
    public function deletePartialRefund(Session $session, int $refundId): void
    {
        $refund = PartialRefund::where('session_id', $session->id)
            ->with(['debt.refund'])->find($refundId);
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        if($refund->debt->refund !== null || $refund->online)
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $refund->delete();
    }
}
