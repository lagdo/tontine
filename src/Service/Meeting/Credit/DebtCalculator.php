<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Closure;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function min;
use function pow;

class DebtCalculator
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

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
        return $this->tenantService->guild()->sessions()
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
        return !$withCurrent ?
            fn(PartialRefund|Refund $refund) => $refund->session->start_at < $current->start_at :
            fn(PartialRefund|Refund $refund) => $refund->session->start_at <= $current->start_at;
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
     * @param Debt $debt
     * @param Session $current
     *
     * @return Session
     */
    private function getLastSessionForInterest(Debt $debt, Session $current): Session
    {
        // We use a join instead of a subquery so we can order the results by session date.
        $closing = $debt->loan->fund->closings()->interest()
            ->select('closings.*')
            ->join('sessions', 'sessions.id', '=', 'closings.session_id')
            ->where('sessions.start_at', '>=', $debt->loan->session->start_at)
            ->where('sessions.start_at', '<', $current->start_at)
            ->orderBy('sessions.start_at', 'desc')
            ->first();
        return $closing !== null ? $closing->session : $current;
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
        $endSession = $this->getLastSessionForInterest($debt, $session);

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
        $endSession = $this->getLastSessionForInterest($debt, $session);

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
     * @param Debt $debt
     *
     * @return bool
     */
    public function debtAmountIsRight(Debt $debt): bool
    {
        // The amount in a debt model is right if it is a principal debt, it has already been
        // totally refunded, it is an interest debt with fixed amount or unique interest rate.
        return $debt->is_principal || $debt->refund || !$debt->loan->recurrent_interest;
    }

    /**
     * Get the amount of a given debt.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtAmount(Debt $debt, Session $session): int
    {
        return $this->debtAmountIsRight($debt) ? $debt->amount :
            ($debt->loan->simple_interest ?
                $this->getSimpleInterestAmount($debt, $session) :
                $this->getCompoundInterestAmount($debt, $session));
    }

    /**
     * @param Debt $debt
     * @param Session $current
     *
     * @return PartialRefund|null
     */
    private function getLastPartialRefund(Debt $debt, Session $current): ?PartialRefund
    {
        // We use a join instead of a subquery so we can order the results by session date.
        return $debt->partial_refunds()
            ->select('partial_refunds.*')
            ->join('sessions', 'sessions.id', '=', 'partial_refunds.session_id')
            ->where('sessions.start_at', '>', $current->start_at)
            ->orderBy('sessions.start_at', 'desc')
            ->first();
    }

    /**
     * Get the max amount that can be paid for a given debt at a given session.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtPayableAmount(Debt $debt, Session $session): int
    {
        if($debt->refund !== null)
        {
            return 0;
        }
        if($debt->is_principal || !$debt->loan->recurrent_interest)
        {
            return $debt->amount - $debt->partial_refunds()->sum('amount');
        }

        // For debts with simple or compound interest, the payable amount calculation
        // must take into account the partial refunds after the current session.
        $lastPartialRefund = $this->getLastPartialRefund($debt, $session);
        return $lastPartialRefund === null ?
            $this->getDebtDueAmount($debt, $session, true) :
            min($this->getDebtDueAmount($debt, $session, true),
                $this->getDebtDueAmount($debt, $lastPartialRefund->session, true));
    }

    /**
     * Get the paid amount for a given debt at a given session.
     *
     * @param Debt $debt
     * @param Session $session
     * @param bool $withCurrent Take the current session into account.
     *
     * @return int
     */
    public function getDebtPaidAmount(Debt $debt, Session $session, bool $withCurrent): int
    {
        $refundFilter = $this->getRefundFilter($session, $withCurrent);
        return ($debt->refund !== null && $refundFilter($debt->refund)) ? $debt->amount :
            $this->getPartialRefunds($debt, $session, $withCurrent)->sum('amount');
    }

    /**
     * Get the unpaid amount for a given debt at a given session.
     *
     * @param Debt $debt
     * @param Session $session
     * @param bool $withCurrent Take the current session into account.
     *
     * @return int
     */
    public function getDebtUnpaidAmount(Debt $debt, Session $session, bool $withCurrent): int
    {
        return $this->getDebtAmount($debt, $session) -
            $this->getDebtPaidAmount($debt, $session, $withCurrent);
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

        return $this->getDebtAmount($debt, $session) -
            $this->getPartialRefunds($debt, $session, $withCurrent)->sum('amount');
    }

    /**
     * Get the total refunded amount.
     *
     * @param Debt $debt
     *
     * @return int
     */
    public function getDebtRefundedAmount(Debt $debt): int
    {
        return $debt->refund !== null ? $debt->amount : $debt->partial_refunds()->sum('amount');
    }
}
