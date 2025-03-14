<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function trans;

class RefundService
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
    public function getDebts(Session $session, Fund $fund, ?bool $onlyPaid,
        int $page = 0): Collection
    {
        return $this->getDebtsQuery($session, $fund, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund', 'refund.session',
                'partial_refunds', 'partial_refunds.session'])
            ->get()
            ->each(function(Debt $debt) use($session) {
                $debt->isEditable = $debt->refund !== null ?
                    $this->canDeleteRefund($debt, $session) :
                    $this->canCreateRefund($debt, $session);
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
     * @param Debt $debt
     * @param Session $session
     *
     * @return bool
     */
    private function canCreateRefund(Debt $debt, Session $session): bool
    {
        // Already refunded
        // Cannot refund the principal debt in the same session as the loan.
        if(!$session->opened || $debt->refund !== null ||
            $debt->is_principal && $debt->loan->session->id === $session->id)
        {
            return false;
        }

        // Cannot refund a recurrent interest debt before the principal.
        if($debt->is_interest && $debt->loan->recurrent_interest)
        {
            return $debt->loan->principal_debt->refund !== null;
        }

        // Cannot refund the principal debt before the last partial refund.
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
            if($debt->is_interest && $debt->loan->recurrent_interest)
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
        // Cannot delete the principal refund if the interest is also refunded.
        if($debt->is_principal && $debt->loan->recurrent_interest)
        {
            return $debt->loan->interest_debt->refund === null;
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
}
