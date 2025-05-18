<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;

use function trans;

class PartialRefundService
{
    use RefundTrait;

    /**
     * @param DebtCalculator $debtCalculator
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param PaymentServiceInterface $paymentService;
     */
    public function __construct(private DebtCalculator $debtCalculator,
        TenantService $tenantService, private LocaleService $localeService,
        FundService $fundService, private PaymentServiceInterface $paymentService)
    {
        $this->tenantService = $tenantService;
        $this->fundService = $fundService;
    }

    /**
     * Get an unpaid debt.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getUnpaidDebt(Session $session, int $debtId): ?Debt
    {
        return $this->getDebtsQuery($session, null, false, false)
            // A debt from a loan created in the current session can be refunded only
            // if it is an interest debt with fixed or unique interest.
            ->where(function(Builder $query) use($session) {
                $query
                    ->whereHas('loan',
                        fn(Builder $q) => $q->where('session_id', '!=', $session->id))
                    ->orWhere(fn(Builder $q) => $q
                        ->interest()
                        ->whereHas('loan', fn(Builder $ql) => $ql->fixedInterest()));
            })
            ->with([
                'partial_refund' => fn($q) => $q->where('session_id', $session->id),
            ])
            ->find($debtId);
    }

    /**
     * @param Session $session
     * @param Debt $debt
     *
     * @return bool
     */
    private function canPartiallyRefund(Session $session, Debt $debt): bool
    {
        return $session->opened && !$debt->refund;
    }

    /**
     * Create a refund.
     *
     * @param Session $session
     * @param Debt $debt
     * @param int $amount
     *
     * @return void
     */
    public function createPartialRefund(Session $session, Debt $debt, int $amount): void
    {
        if(!$this->canPartiallyRefund($session, $debt))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_create'));
        }
        // A partial refund must not totally refund a debt
        if($amount >= $this->debtCalculator->getDebtPayableAmount($debt, $session))
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
     * @param Session $session
     * @param PartialRefund $refund
     *
     * @return bool
     */
    private function canModifyPartialRefund(Session $session, PartialRefund $refund): bool
    {
        // A partial refund cannot be updated or deleted if the debt is already refunded.
        return $session->opened && $refund->debt->refund === null &&
            $this->paymentService->isEditable($refund);
    }

    /**
     * Update a refund.
     *
     * @param Session $session The session
     * @param PartialRefund $refund
     * @param int $amount
     *
     * @return void
     */
    public function updatePartialRefund(Session $session, PartialRefund $refund, int $amount): void
    {
        if(!$this->canModifyPartialRefund($session, $refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_update'));
        }

        // A partial refund must not totally refund a debt
        $maxAmount = $refund->amount +
            $this->debtCalculator->getDebtPayableAmount($refund->debt, $session);
        if($amount >= $maxAmount)
        {
            throw new MessageException(trans('meeting.refund.errors.pr_amount'));
        }

        $refund->update(['amount' => $amount]);
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param PartialRefund $refund
     *
     * @return void
     */
    public function deletePartialRefund(Session $session, PartialRefund $refund): void
    {
        if(!$this->canModifyPartialRefund($session, $refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }

        $refund->delete();
    }
}
