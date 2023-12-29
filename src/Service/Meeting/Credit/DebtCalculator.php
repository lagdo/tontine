<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Siak\Tontine\Model\Debt;
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
     * @param Session $session
     * @param Debt $debt
     *
     * @return bool
     */
    public function debtIsEditable(Session $session, Debt $debt): bool
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
        if($debt->is_interest && !$debt->loan->fixed_interest)
        {
            // Cannot refund the interest debt before the principal.
            return $debt->loan->principal_debt->refund !== null;
        }

        // Not yet refunded. Canot be refunded before the last partial refund.
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
     * @param Session $session
     * @param Debt $debt
     *
     * @return int
     */
    private function getSimpleInterestAmount(Session $session, Debt $debt): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        $interestRate = $debt->loan->interest_rate / 10000;

        $interestAmount = 0;
        $fromSession = $debt->loan->session;
        // Take refunds before the current session and filter by session date.
        $partialRefunds = $principalDebt->partial_refunds
            ->filter(function($refund) use($session) {
                return $refund->session->start_at < $session->start_at;
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

        $lastSession = $this->getLastSession($session, $principalDebt);
        $sessionCount = $this->getSessionCount($fromSession, $lastSession);

        return $interestAmount + (int)($loanAmount * $interestRate * $sessionCount);
    }

    /**
     * Get the compound interest amount.
     *
     * @param Session $session
     * @param Debt $debt
     *
     * @return int
     */
    private function getCompoundInterestAmount(Session $session, Debt $debt): int
    {
        $principalDebt = $debt->loan->principal_debt;
        $loanAmount = $principalDebt->amount;
        $interestRate = $debt->loan->interest_rate / 10000;

        $interestAmount = 0;
        $fromSession = $debt->loan->session;
        // Take refunds before the current session and filter by session date.
        $partialRefunds = $principalDebt->partial_refunds
            ->filter(function($refund) use($session) {
                return $refund->session->start_at < $session->start_at;
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

        $lastSession = $this->getLastSession($session, $principalDebt);
        $sessionCount = $this->getSessionCount($fromSession, $lastSession);

        return $interestAmount + (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
    }

    /**
     * Get the amount of a given debt.
     *
     * @param Session $session
     * @param Debt $debt
     *
     * @return int
     */
    public function getDebtAmount(Session $session, Debt $debt): int
    {
        if($debt->is_principal || $debt->refund || $debt->loan->fixed_interest)
        {
            return $debt->amount;
        }

        return $debt->loan->simple_interest ?
            $this->getSimpleInterestAmount($session, $debt) :
            $this->getCompoundInterestAmount($session, $debt);
    }

    /**
     * Get the amount due of a given debt.
     *
     * @param Session $session
     * @param Debt $debt
     *
     * @return int
     */
    public function getDebtDueAmount(Session $session, Debt $debt): int
    {
        return $this->getDebtAmount($session, $debt) - $debt->partial_refunds->sum('amount');
    }
}
