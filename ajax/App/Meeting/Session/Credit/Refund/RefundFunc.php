<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\FuncComponent;
use Siak\Tontine\Model\Debt as DebtModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function trans;

/**
 * @databag meeting.refund
 * @before getFund
 */
class RefundFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund';

    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * The constructor
     *
     * @param RefundService $refundService
     */
    public function __construct(protected RefundService $refundService)
    {}

    /**
     * @param int $debtId
     *
     * @return DebtModel|null
     */
    private function refreshDebt(int $debtId): ?DebtModel
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $debt = $this->refundService->getFundDebt($session, $fund, $debtId);
        // Al already refunded debt will not be returned here.
        if($debt !== null)
        {
            $this->stash()->set('meeting.refund.debt', $debt);
            $this->cl(RefundItem::class)->item($debt->id)->render();
        }
        return $debt;
    }

    /**
     * @param int $debtId
     *
     * @return void
     */
    private function refreshDebts(int $debtId): void
    {
        // Refresh the modified debt in the table.
        $debt = $this->refreshDebt($debtId);
        if(!$debt)
        {
            return;
        }

        // When a debt from a loan with recurring interest is changed,
        // the other debt of the same loan must also be refreshed.
        $loan = $debt->loan;
        if($loan->recurrent_interest)
        {
            $this->refreshDebt($debt->is_principal ?
                $loan->interest_debt->id : $loan->principal_debt->id);
        }
    }

    /**
     * @di $validator
     */
    public function create(string $debtId)
    {
        $debt = $this->refundService->getDebt($debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->refundService->createRefund($debt, $session);

        $this->alert()->success(trans('meeting.refund.messages.created'));
        $this->refreshDebts($debtId);
    }

    public function delete(int $debtId)
    {
        $debt = $this->refundService->getDebt($debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->refundService->deleteRefund($debt, $session);

        $this->alert()->success(trans('meeting.refund.messages.deleted'));
        $this->refreshDebts($debtId);
    }
}
