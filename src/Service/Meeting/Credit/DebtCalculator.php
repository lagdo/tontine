<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Closure;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function pow;

class DebtCalculator
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get the amount due of a given debt.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    public function debtIsEditable(Debt $debt, Session $session): bool
    {
        if(!$session->opened ||
            ($debt->is_principal && $debt->loan->session->id === $session->id))
        {
            // Cannot refund the principal debt in the same session.
            return false;
        }
        // Refunded
        if($debt->refund !== null)
        {
            // Editable only if refunded in the current session
            return $debt->refund->session_id === $session->id;
        }
        // Not yet refunded.
        if($debt->is_interest && !$debt->loan->fixed_interest)
        {
            // Cannot refund the interest debt before the principal.
            return $debt->loan->principal_debt->refund !== null;
        }

        // Cannot be refunded before the last partial refund.
        $lastRefund = $debt->partial_refunds->sortByDesc('session.start_at')->first();
        return !$lastRefund || $lastRefund->session->start_at < $session->start_at;
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
        return $this->tenantService->tontine()->sessions()
            ->whereDate('start_at', '>', $fromSession->start_at->format('Y-m-d'))
            ->whereDate('start_at', '<=', $toSession->start_at->format('Y-m-d'))
            ->count();
    }

    /**
     * Get the last session for interest calculation
     *
     * @param Debt $debt
     * @param Session $currentSession
     *
     * @return Session
     */
    private function getLastSession(Debt $debt, Session $currentSession): Session
    {
        return $debt->refund &&
            $debt->refund->session->start_at < $currentSession->start_at ?
            $debt->refund->session : $currentSession;
    }

    /**
     * @param Session $current
     * @param bool $withCurrent Also take the refunds in the current session.
     *
     * @return Closure
     */
    private function getRefundFilter(Session $current, bool $withCurrent): Closure
    {
        return $withCurrent ?
            function(PartialRefund|Refund $refund) use($current) {
                return $refund->session->start_at <= $current->start_at;
            } :
            function(PartialRefund|Refund $refund) use($current) {
                return $refund->session->start_at < $current->start_at;
            };
    }

    /**
     * @param Debt $debt
     * @param Session $current
     * @param bool $withCurrent Also take the refunds in the current session.
     *
     * @return Collection
     */
    private function getPartialRefunds(Debt $debt, Session $current, bool $withCurrent): Collection
    {
        return $debt->partial_refunds->filter($this->getRefundFilter($current, $withCurrent));
    }

    /**
     * Get the simple interest amount.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    private function getSimpleInterestAmount(Debt $debt, Session $session): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        // The interest rate is multiplied by 100 in the database.
        $interestRate = $debt->loan->interest_rate / 10000;
        $interestAmount = 0;

        $startSession = $debt->loan->session;
        $endSession = $session;

        // Take refunds before the end session and sort by session date.
        $partialRefunds = $this->getPartialRefunds($principalDebt, $endSession, false)
            ->sortBy('session.start_at');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($startSession, $refund->session);
            $interestAmount += (int)($loanAmount * $interestRate * $sessionCount);
            // For the next loop
            $loanAmount -= $refund->amount;
            $startSession = $refund->session;
        }

        $lastSession = $this->getLastSession($principalDebt, $endSession);
        $sessionCount = $this->getSessionCount($startSession, $lastSession);

        return $interestAmount + (int)($loanAmount * $interestRate * $sessionCount);
    }

    /**
     * Get the compound interest amount.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    private function getCompoundInterestAmount(Debt $debt, Session $session): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        // The interest rate is multiplied by 100 in the database.
        $interestRate = $debt->loan->interest_rate / 10000;
        $interestAmount = 0;

        $startSession = $debt->loan->session;
        $endSession = $session;

        // Take refunds before the current session and sort by session date.
        $partialRefunds = $this->getPartialRefunds($principalDebt, $endSession, false)
            ->sortBy('session.start_at');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($startSession, $refund->session);
            $interestAmount += (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
            // For the next loop
            $loanAmount -= $refund->amount - $interestAmount;
            $startSession = $refund->session;
        }

        $lastSession = $this->getLastSession($principalDebt, $endSession);
        $sessionCount = $this->getSessionCount($startSession, $lastSession);

        return $interestAmount + (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
    }

    /**
     * Get the total amount due of a given debt.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtTotalAmount(Debt $debt, Session $session): int
    {
        if($debt->is_principal || $debt->refund || $debt->loan->fixed_interest)
        {
            return $debt->amount;
        }

        return $debt->loan->simple_interest ?
            $this->getSimpleInterestAmount($debt, $session) :
            $this->getCompoundInterestAmount($debt, $session);
    }

    /**
     * Get the total paid amount for a given debt.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtPaidAmount(Debt $debt, Session $session): int
    {
        return $debt->refund !== null ? $debt->amount :
            $this->getPartialRefunds($debt, $session, true)->sum('amount');
    }

    /**
     * Get the total unpaid amount for a given debt.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtUnpaidAmount(Debt $debt, Session $session): int
    {
        return $this->getDebtTotalAmount($debt, $session) -
            $this->getDebtPaidAmount($debt, $session);
    }

    /**
     * Get the amount due of a given debt before the given session.
     *
     * @param Debt $debt
     * @param Session $session
     * @param bool $withCurrent Take the current session into account.
     *
     * @return int
     */
    public function getDebtDueAmount(Debt $debt, Session $session, bool $withCurrent): int
    {
        $refundFilter = $this->getRefundFilter($session, $withCurrent);
        if($debt->refund !== null && $refundFilter($debt->refund))
        {
            return 0; // The debt was refunded before the current session.
        }

        return $this->getDebtTotalAmount($debt, $session) -
            $this->getPartialRefunds($debt, $session, $withCurrent)->sum('amount');
    }
}
