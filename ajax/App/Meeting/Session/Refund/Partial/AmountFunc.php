<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Ajax\App\Meeting\Session\Refund\Total\Refund as TotalRefund;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

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
     * @di $validator
     */
    public function save(int $debtId, string $amount)
    {
        // Validation
        $values = $this->validator->validateItem(['debt' => $debtId, 'amount' => $amount]);

        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $values['amount'] === 0 ?
            $this->refundService->deletePartialRefund($debt->partial_refund, $session) :
            $this->refundService->savePartialRefund($debt, $session, $values['amount']);

        // Refresh the refunds page
        $this->cl(Debt::class)->render();
        // The fund needs to be shared with the other page.
        $this->stash()->set('meeting.refund.final.fund', $this->getStashedFund());
        $this->cl(TotalRefund::class)->render();
    }
}
