<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Ajax\App\Meeting\Session\Refund\Total\Refund as TotalRefund;
use Siak\Tontine\Model\Debt as DebtModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function trans;

/**
 * @databag meeting.refund.partial
 * @before getFund
 */
class AmountFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.partial';

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

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

    /**
     * @di $localeService
     */
    public function edit(int $debtId)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.refund.partial.debt', $debt);
        $this->stash()->set('meeting.refund.partial.edit', true);
        $this->cl(Amount::class)->item($debt->id)->render();
    }

    /**
     * @param DebtModel $debt
     * @param SessionModel $session
     * @param int $amount
     *
     * @return void
     */
    private function _save(DebtModel $debt, SessionModel $session, int $amount): void
    {
        if($amount === 0)
        {
            $this->refundService->deletePartialRefund($debt->partial_refund, $session);
            $this->alert()->success(trans('meeting.refund.messages.deleted'));
            return;
        }
        if($debt->partial_refund === null)
        {
            $this->refundService->createPartialRefund($debt, $session, $amount);
            $this->alert()->success(trans('meeting.refund.messages.created'));
            return;
        }
        $this->refundService->updatePartialRefund($debt->partial_refund, $session, $amount);
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
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
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

        $this->_save($debt, $session, $values['amount']);

        // Refresh the refunds page
        $this->cl(Debt::class)->render();
        // The fund needs to be shared with the other page.
        $this->stash()->set('meeting.refund.final.fund', $this->getStashedFund());
        $this->cl(TotalRefund::class)->render();
    }
}
