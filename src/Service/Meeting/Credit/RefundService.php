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
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;

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
     * @param Fund|null $fund
     * @param bool|null $onlyPaid
     *
     * @return int
     */
    public function getDebtCount(Session $session, ?Fund $fund, ?bool $onlyPaid): int
    {
        return $this->getDebtsQuery($session, $fund, $onlyPaid, false)->count();
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param Fund|null $fund
     * @param bool|null $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getDebts(Session $session, ?Fund $fund,
        ?bool $onlyPaid, int $page = 0): Collection
    {
        return $this->getDebtsQuery($session, $fund, $onlyPaid, true)
            ->orderBy('member_defs.name')
            ->orderBy('debts.id')
            ->page($page, $this->tenantService->getLimit())
            ->get()
            ->each(fn(Debt $debt) => $this->fillDebt($debt, $session));
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param array $ids
     *
     * @return Collection
     */
    public function getDebtsByIds(Session $session, array $ids): Collection
    {
        return $this->getDebtsQuery($session, null, false, true)
            ->whereIn('id', $ids)
            ->get()
            ->each(fn(Debt $debt) => $this->fillDebt($debt, $session))
            ->keyBy('id');
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
            throw new MessageException(trans('meeting.refund.errors.cannot_create'));
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
     * Delete a refund.
     *
     * @param Debt $debt
     * @param Session $session The session
     *
     * @return void
     */
    public function deleteRefund(Debt $debt, Session $session): void
    {
        if(!$this->canDeleteRefund($debt, $session) ||
            !$this->paymentService->isEditable($debt->refund))
        {
            throw new MessageException(trans('meeting.refund.errors.cannot_delete'));
        }
        $debt->refund->delete();
    }
}
