<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

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
     * @param Session $session The session
     * @param Fund $fund
     *
     * @return Builder|Relation
     */
    private function getQuery(Session $session, Fund $fund): Builder|Relation
    {
        return $session->partial_refunds()
            ->whereHas('debt', function(Builder|Relation $query) use($fund) {
                $query->whereHas('loan', function(Builder|Relation $query) use($fund) {
                    $query->where('fund_id', $fund->id);
                });
            });
    }

    /**
     * Get the number of partial refunds.
     *
     * @param Session $session The session
     * @param Fund $fund
     *
     * @return int
     */
    public function getPartialRefundCount(Session $session, Fund $fund): int
    {
        return $this->getQuery($session, $fund)->count();
    }

    /**
     * Get the partial refunds.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param int $page
     *
     * @return Collection
     */
    public function getPartialRefunds(Session $session, Fund $fund, int $page = 0): Collection
    {
        return $this->getQuery($session, $fund)
            ->page($page, $this->tenantService->getLimit())
            ->with(['debt.refund', 'debt.loan.member', 'debt.loan.session'])
            ->orderBy('id')
            ->get()
            ->each(fn(PartialRefund $refund) => $refund->debtAmount =
                $this->debtCalculator->getDebtDueAmount($refund->debt, $session, false))
            ->sortBy('debt.loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * @param Session $session The session
     * @param Fund $fund
     * @param bool $with
     *
     * @return Builder|Relation
     */
    private function getUnpaidDebtsQuery(Session $session, Fund $fund, bool $with): Builder|Relation
    {
        return $this->getDebtsQuery($session, $fund, false, false)
            // A debt from a loan created in the current session can be refunded only
            // if it is an interest debt with fixed or unique interest.
            ->where(function(Builder $query) use($session) {
                $query->whereHas('loan', function(Builder $query) use($session) {
                    $query->where('session_id', '!=', $session->id);
                })->orWhere(function(Builder $query) {
                    $query->interest()
                        ->whereHas('loan', fn(Builder $q) => $q->fixedInterest());
                });
            })
            ->when($with, function(Builder $query) use($session) {
                $query->with([
                    'partial_refund' => fn($q) => $q->where('session_id', $session->id),
                ]);
            });
    }

    /**
     * Count the unpaid debts.
     *
     * @param Session $session The session
     * @param Fund $fund
     *
     * @return int
     */
    public function getUnpaidDebtCount(Session $session, Fund $fund): int
    {
        return $this->getUnpaidDebtsQuery($session, $fund, false)->count();
    }

    /**
     * Get the unpaid debts.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param int $page
     *
     * @return Collection
     */
    public function getUnpaidDebts(Session $session, Fund $fund, int $page = 0): Collection
    {
        return $this->getUnpaidDebtsQuery($session, $fund, true)
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get an unpaid debt.
     *
     * @param Session $session The session
     * @param Fund $fund
     *
     * @return Debt|null
     */
    public function getUnpaidDebt(Session $session, Fund $fund, int $debtId): ?Debt
    {
        return $this->getUnpaidDebtsQuery($session, $fund, true)->find($debtId);
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
     * Find a refund.
     *
     * @param Session $session The session
     * @param int $refundId
     *
     * @return PartialRefund
     */
    public function getPartialRefund(Session $session, int $refundId): PartialRefund
    {
        $refund = PartialRefund::where('session_id', $session->id)
            ->with(['debt.refund'])
            ->find($refundId);
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }

        return $refund;
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
