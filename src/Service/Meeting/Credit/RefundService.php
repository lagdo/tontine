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
    private function getDebt(int $debtId): ?Debt
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
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function createRefund(Session $session, int $debtId): void
    {
        $debt = $this->getDebt($debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('tontine.loan.errors.not_found'));
        }
        if(!$this->debtCalculator->debtIsEditable($debt, $session))
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
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $debtId): void
    {
        $refund = Refund::with('debt')->where('session_id', $session->id)
            ->where('debt_id', $debtId)->first();
        if(!$refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
        }
        if(!$this->paymentService->isEditable($refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        if(!$this->debtCalculator->debtIsEditable($refund->debt, $session))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_refund'));
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
                return $debts->concat($this->getDebts($session, $fund, false));
            }, collect())
            ->filter(function($debt) use($session) {
                return $this->debtCalculator->debtIsEditable($debt, $session);
            })
            ->keyBy('id')
            ->map(function($debt) use($session) {
                $fundTitle = $this->fundService->getFundTitle($debt->loan->fund);
                $unpaidAmount = $this->debtCalculator->getDebtUnpaidAmount($debt, $session);

                return $debt->loan->member->name . " - $fundTitle - " . $debt->loan->session->title .
                    ' - ' . trans('meeting.loan.labels.' . $debt->type) .
                    ' - ' . $this->localeService->formatMoney($unpaidAmount, true);
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
        $debt = $this->getDebt($debtId);
        if(!$debt || $debt->refund)
        {
            throw new MessageException(trans('meeting.refund.errors.not_found'));
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
