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
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canCreatePartialRefund(Debt $debt, Session $session): bool
    {
        // Cannot refund the principal debt in the same session.
        if(!$session->opened || $debt->refund !== null ||
            ($debt->is_principal && $debt->loan->session->id === $session->id))
        {
            return false;
        }

        return true;
    }

    /**
     * @param Session $session The session
     * @param Fund $fund
     *
     * @return Builder|Relation
     */
    private function getUnpaidDebtsQuery(Session $session, Fund $fund): Builder|Relation
    {
        return $this->getDebtsQuery($session, $fund, false)
        ->with([
            'partial_refund' => fn($query) => $query->where('session_id', $session->id),
        ]);
    }

    /**
     * Get the unpaid debts.
     *
     * @param Fund $fund
     * @param Session $session The session
     *
     * @return Collection
     */
    public function getUnpaidDebts(Fund $fund, Session $session): Collection
    {
        return $this->getUnpaidDebtsQuery($session, $fund)
            ->get()
            ->filter(fn(Debt $debt) => $this->canCreatePartialRefund($debt, $session));
    }

    /**
     * Get an unpaid debt.
     *
     * @param Fund $fund
     * @param Session $session The session
     *
     * @return Debt|null
     */
    public function getUnpaidDebt(Fund $fund, Session $session, int $debtId): ?Debt
    {
        return $this->getUnpaidDebtsQuery($session, $fund)->find($debtId);
    }

    /**
     * Create or update a refund.
     *
     * @param Debt $debt
     * @param Session $session The session
     * @param int $amount
     *
     * @return void
     */
    public function savePartialRefund(Debt $debt, Session $session, int $amount): void
    {
        $debt->partial_refund === null ?
            $this->createPartialRefund($debt, $session, $amount) :
            $this->updatePartialRefund($debt->partial_refund, $session, $amount);
    }

    /**
     * Create a refund.
     *
     * @param Debt $debt
     * @param Session $session The session
     * @param int $amount
     *
     * @return void
     */
    public function createPartialRefund(Debt $debt, Session $session, int $amount): void
    {
        if(!$this->canCreatePartialRefund($debt, $session))
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
     * @param PartialRefund $refund
     * @param Session $session
     *
     * @return bool
     */
    private function canModifyPartialRefund(PartialRefund $refund, Session $session): bool
    {
        // A partial refund cannot be updated or deleted if the debt is already refunded.
        if(!$session->opened || $refund->debt->refund !== null ||
            !$this->paymentService->isEditable($refund))
        {
            return false;
        }

        return true;
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
     * @param PartialRefund $refund
     * @param Session $session The session
     * @param int $amount
     *
     * @return void
     */
    public function updatePartialRefund(PartialRefund $refund, Session $session, int $amount): void
    {
        if(!$this->canModifyPartialRefund($refund, $session))
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
     * @param PartialRefund $refund
     * @param Session $session The session
     *
     * @return void
     */
    public function deletePartialRefund(PartialRefund $refund, Session $session): void
    {
        if(!$this->canModifyPartialRefund($refund, $session))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }

        $refund->delete();
    }
}
