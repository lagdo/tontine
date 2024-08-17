<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function collect;
use function trans;

class RefundService
{
    /**
     * @param DebtCalculator $debtCalculator
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     * @param SessionService $sessionService
     * @param FundService $fundService
     * @param PaymentServiceInterface $paymentService;
     */
    public function __construct(private DebtCalculator $debtCalculator,
        private TenantService $tenantService, private LocaleService $localeService,
        private SessionService $sessionService, private FundService $fundService,
        private PaymentServiceInterface $paymentService)
    {}

    /**
     * @param Session $session The session
     * @param Fund $fund
     * @param bool|null $onlyPaid
     *
     * @return Builder|Relation
     */
    private function getDebtsQuery(Session $session, Fund $fund, ?bool $onlyPaid): Builder|Relation
    {
        $prevSessions = $this->fundService->getFundSessionIds($session, $fund)
            ->filter(fn(int $sessionId) => $sessionId !== $session->id);

        return Debt::whereHas('loan', function(Builder $query) use($fund) {
                $query->where('fund_id', $fund->id);
            })
            ->when($onlyPaid === false, function(Builder $query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function(Builder $query) {
                return $query->whereHas('refund');
            })
            ->where(function(Builder $query) use($session, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function(Builder $query) use($session) {
                    $query->whereHas('loan', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function(Builder $query) use($session, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($prevSessions) {
                        $query->whereIn('session_id', $prevSessions);
                    })
                    ->where(function(Builder $query) use($session) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
                });
            });
    }

    /**
     * Get the number of debts.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param bool|null $onlyPaid
     *
     * @return int
     */
    public function getDebtCount(Session $session, Fund $fund, ?bool $onlyPaid): int
    {
        return $this->getDebtsQuery($session, $fund, $onlyPaid)->count();
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param bool|null $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getDebts(Session $session, Fund $fund, ?bool $onlyPaid, int $page = 0): Collection
    {
        return $this->getDebtsQuery($session, $fund, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund', 'refund.session',
                'partial_refunds', 'partial_refunds.session'])
            ->get()
            ->each(function(Debt $debt) use($session) {
                $debt->isEditable = $debt->refund !== null ?
                    $this->canDeleteRefund($debt, $session) : $this->canCreateRefund($debt, $session);
            })
            ->sortBy('loan.member.name', SORT_LOCALE_STRING)
            ->values();
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
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getDebt(int $debtId): ?Debt
    {
        return Debt::whereDoesntHave('refund')
            ->whereHas('loan', function(Builder $query) {
                $query->whereHas('member', function(Builder $query) {
                    $query->where('tontine_id', $this->tenantService->tontine()->id);
                });
            })
            ->find($debtId);
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canCreateRefund(Debt $debt, Session $session): bool
    {
        // Already refunded
        // Cannot refund the principal debt in the same session.
        if(!$session->opened || $debt->refund !== null ||
            $debt->is_principal && $debt->loan->session->id === $session->id)
        {
            return false;
        }
        // Cannot refund the interest debt before the principal.
        if($debt->is_interest && !$debt->loan->fixed_interest)
        {
            return $debt->loan->principal_debt->refund !== null;
        }

        // Cannot be refunded before the last partial refund.
        $lastRefund = $debt->partial_refunds->sortByDesc('session.start_at')->first();
        return !$lastRefund || $lastRefund->session->start_at < $session->start_at;
    }

    /**
     * Create a refund.
     *
     * @param Debt $debt
     * @param Session $session The session
     *
     * @return void
     */
    public function createRefund(Debt $debt, Session $session): void
    {
        if(!$this->canCreateRefund($debt, $session))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_refund'));
        }

        $refund = new Refund();
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        DB::transaction(function() use($debt, $session, $refund) {
            $refund->save();
            // For simple or compound interest, also save the final amount.
            if($debt->is_interest && !$debt->loan->fixed_interest)
            {
                $debt->amount = $this->debtCalculator->getDebtDueAmount($debt, $session, true);
                $debt->save();
            }
        });
    }

    /**
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canDeleteRefund(Debt $debt, Session $session): bool
    {
        // A refund can only be deleted in the same session it was created.
        if(!$session->opened || !$debt->refund || $debt->refund->session_id !== $session->id)
        {
            return false;
        }

        return true;
    }

    /**
     * Delete a refund.
     *
     * @param Debt $debt
     * @param Session $session The session
     *
     * @return void
     */
    public function deleteRefund(Debt $debt, Session $session): void
    {
        if(!$this->canDeleteRefund($debt, $session))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_refund'));
        }
        if(!$this->paymentService->isEditable($debt->refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $debt->refund->delete();
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
            ->orderBy('id')
            ->get()
            ->each(function(PartialRefund $refund) use($session) {
                $refund->debtAmount = $this->debtCalculator->getDebtDueAmount($refund->debt, $session, false);
            })
            ->sortBy('debt.loan.member.name', SORT_LOCALE_STRING)
            ->values();
    }

    /**
     * Get the ids of all active funds.
     *
     * @return Collection
     */
    public function getActiveFundIds(): Collection
    {
        $tontine = $this->tenantService->tontine();
        return $tontine->funds()->active()
            ->pluck('id')
            ->prepend($tontine->default_fund->id);
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
     * Get debt list for dropdown.
     *
     * @param Session $session The session
     *
     * @return Collection
     */
    public function getUnpaidDebtList(Session $session): Collection
    {
        return $this->fundService->getActiveFunds()
            ->reduce(function(Collection $debts, Fund $fund) use($session) {
                $fundDebts = $this->getDebtsQuery($session, $fund, false)
                    ->with(['loan', 'loan.member', 'loan.session', 'refund', 'refund.session'])
                    ->get()
                    ->sortBy('loan.member.name', SORT_LOCALE_STRING)
                    ->values();
                return $debts->concat($fundDebts);
            }, collect())
            ->filter(function($debt) use($session) {
                return $this->canCreatePartialRefund($debt, $session);
            })
            ->keyBy('id')
            ->map(function($debt) use($session) {
                $loan = $debt->loan;
                $fundTitle = $this->fundService->getFundTitle($loan->fund);
                $unpaidAmount = $this->debtCalculator->getDebtUnpaidAmount($debt, $session);

                return $loan->member->name . " - $fundTitle - " . $loan->session->title .
                    ' - ' . trans('meeting.loan.labels.' . $debt->type) .
                    ' - ' . $this->localeService->formatMoney($unpaidAmount, true);
            });
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
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        // A partial refund must not totally refund a debt
        if($amount >= $this->debtCalculator->getDebtDueAmount($debt, $session, true))
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
    private function canDeletePartialRefund(PartialRefund $refund, Session $session): bool
    {
        // A partial refund cannot be deleted if the debt is already refunded.
        if(!$session->opened || $refund->debt->refund !== null)
        {
            return false;
        }

        return true;
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
        if($refund->debt->refund !== null || !$this->paymentService->isEditable($refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $refund->delete();
    }
}
