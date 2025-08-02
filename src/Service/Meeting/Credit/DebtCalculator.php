<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Closure;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;

use function pow;

class DebtCalculator
{
    /**
     * Count the sessions.
     *
     * @param Debt $debt
     * @param Session $fromSession The session to start from
     * @param Session $toSession The session to end to
     *
     * @return int
     */
    private function getSessionCount(Debt $debt, Session $fromSession, Session $toSession): int
    {
        return $debt->loan->fund->sessions
            ->filter(fn($session) => $session->day_date <= $toSession->day_date &&
                $session->day_date > $fromSession->day_date)
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
            $debt->refund->session->day_date < $currentSession->day_date ?
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
            fn(PartialRefund|Refund $refund) => $refund->session->day_date < $current->day_date :
            fn(PartialRefund|Refund $refund) => $refund->session->day_date <= $current->day_date;
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
        $endSession = $debt->loan->fund->interest;
        return $current->day_date < $endSession->day_date ? $current : $endSession;
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
            ->sortBy('session.day_date');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($debt, $startSession, $refund->session);
            $interestAmount += (int)($loanAmount * $interestRate * $sessionCount);
            // For the next loop
            $loanAmount -= $refund->amount;
            $startSession = $refund->session;
        }

        $lastSession = $this->getLastSession($principalDebt, $endSession);
        $sessionCount = $this->getSessionCount($debt, $startSession, $lastSession);

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
            ->sortBy('session.day_date');
        foreach($partialRefunds as $refund)
        {
            $sessionCount = $this->getSessionCount($debt, $startSession, $refund->session);
            $interestAmount += (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
            // For the next loop
            $loanAmount -= $refund->amount - $interestAmount;
            $startSession = $refund->session;
        }

        $lastSession = $this->getLastSession($principalDebt, $endSession);
        $sessionCount = $this->getSessionCount($debt, $startSession, $lastSession);

        return $interestAmount + (int)($loanAmount * (pow(1 + $interestRate, $sessionCount) - 1));
    }

    /**
     * @param Debt $debt
     *
     * @return bool
     */
    private function debtAmountIsFixed(Debt $debt): bool
    {
        // The amount in a debt model is fixed if it is a principal debt,
        // or it is an interest debt with fixed amount or unique interest rate.
        return $debt->is_principal || !$debt->loan->recurrent_interest;
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
        return $this->debtAmountIsFixed($debt) ? $debt->amount :
            ($debt->loan->simple_interest ?
                $this->getSimpleInterestAmount($debt, $session) :
                $this->getCompoundInterestAmount($debt, $session));
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
     * Get the max amount that can be paid for a given debt at a given session.
     *
     * @param Debt $debt
     * @param Session $session
     *
     * @return int
     */
    public function getDebtPayableAmount(Debt $debt, Session $session): int
    {
        return $this->getDebtDueAmount($debt, $session, false);
    }

    /**
     * Get the total refunded amount.
     *
     * @param Debt $debt
     *
     * @return int
     */
    public function getDebtTotalPaidAmount(Debt $debt): int
    {
        return $debt->refund !== null ? $debt->amount : $debt->partial_refunds->sum('amount');
    }

    /**
     * Get the amount due after the current session
     *
     * @param Debt $debt
     * @param Session $session
     * @param int $dueBefore
     *
     * @return int
     */
    private function getAmountDueAfter(Debt $debt, Session $session, int $dueBefore): int
    {
        $refundFilter = $this->getRefundFilter($session, true);
        if($debt->refund !== null && $refundFilter($debt->refund))
        {
            return 0; // The debt was refunded before the current session.
        }

        return $dueBefore - ($debt->partial_refund?->amount ?? 0);
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return array
     */
    public function getAmounts(Debt $debt, Session $session): array
    {
        // The total debt amount.
        $debtAmount = $this->getDebtAmount($debt, $session);
        // The total paid amount.
        $totalPaidAmount = $this->getDebtTotalPaidAmount($debt);
        // The amount paid before the session.
        $paidAmount = $this->getDebtPaidAmount($debt, $session, false);
        // The amount due before the session;
        $amountDueBefore = $debtAmount - $paidAmount;
        // The amount due after the session;
        $amountDueAfter = $this->getAmountDueAfter($debt, $session, $amountDueBefore);

        return [
            'debt' => $debt,
            'session' => $session,
            'debtAmount' => $debtAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'paidAmount' => $paidAmount,
            'amountDueBeforeSession' => $amountDueBefore,
            'amountDueAfterSession' => $amountDueAfter,
            'payableAmount' => $amountDueBefore,
        ];
    }
}
