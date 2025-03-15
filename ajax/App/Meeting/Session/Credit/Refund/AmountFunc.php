<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Model\Debt as DebtModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function trans;

/**
 * @databag meeting.refund
 * @before getFund
 */
class AmountFunc extends FuncComponent
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
     * @param PartialRefundService $refundService
     */
    public function __construct(protected PartialRefundService $refundService)
    {}

    public function edit(int $debtId)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $debt = $this->refundService->getUnpaidDebt($session, $fund, $debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.refund.debt', $debt);
        $this->stash()->set('meeting.refund.edit', true);
        $this->cl(Amount::class)->item($debt->id)->render();
    }

    /**
     * @param SessionModel $session
     * @param DebtModel $debt
     * @param int $amount
     *
     * @return void
     */
    private function _save(SessionModel $session, DebtModel $debt, int $amount): void
    {
        if($amount === 0)
        {
            $this->refundService->deletePartialRefund($session, $debt->partial_refund);
            $this->alert()->success(trans('meeting.refund.messages.deleted'));
            return;
        }
        if($debt->partial_refund === null)
        {
            $this->refundService->createPartialRefund($session, $debt, $amount);
            $this->alert()->success(trans('meeting.refund.messages.created'));
            return;
        }
        $this->refundService->updatePartialRefund($session, $debt->partial_refund, $amount);
        $this->alert()->success(trans('meeting.refund.messages.updated'));
    }

    /**
     * @di $validator
     */
    public function save(int $debtId, string $amount)
    {
        // Validation
        $values = $this->validator->validateItem([
            'debt' => $debtId,
            'amount' => $amount === '' ? '0' : $amount,
        ]);

        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $debt = $this->refundService->getUnpaidDebt($session, $fund, $debtId);
        if(!$debt)
        {
            $this->alert()->error(trans('meeting.loan.errors.not_found'));
            return;
        }
        if(!$debt->partial_refund && $values['amount'] === 0)
        {
            $this->alert()->error(trans('meeting.refund.errors.nul_amount'));
            return;
        }

        $this->_save($session, $debt, $values['amount']);

        $debt = $this->refundService->getFundDebt($session, $fund, $debtId);
        $this->stash()->set('meeting.refund.debt', $debt);
        $this->cl(RefundItem::class)->item($debt->id)->render();
    }
}
