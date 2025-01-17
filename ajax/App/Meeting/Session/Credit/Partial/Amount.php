<?php

namespace Ajax\App\Meeting\Session\Credit\Partial;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

/**
 * @databag partial.refund
 * @before getFund
 */
class Amount extends MeetingComponent
{
    /**
     * @var string
     */
    protected $overrides = Refund::class;

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
     * @param FundService $fundService
     * @param PartialRefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected PartialRefundService $refundService)
    {}

    protected function getFund()
    {
        if($this->target()->method() === 'fund')
        {
            $this->bag('partial.refund')->set('fund.id', $this->target()->args()[0]);
        }
        $fundId = $this->bag('partial.refund')->get('fund.id');
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.refund.fund', $fund);
    }

    public function fund(int $fundId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.refund.fund');

        return $this->renderView('pages.meeting.refund.partial.amount.list', [
            'session' => $session,
            'debts' => $this->refundService->getUnpaidDebts($fund, $session),
        ]);
    }

    /**
     * @di $localeService
     */
    public function edit(int $debtId)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.refund.fund');
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
        if(!$debt)
        {
            $this->alert()->warning(trans('meeting.loan.errors.not_found'));
            return;
        }

        $html = $this->renderView('pages.meeting.refund.partial.amount.edit', [
            'debt' => $debt,
            'amount' => $this->localeService->getMoneyValue($debt->partial_refund->amount),
        ]);
        $this->response->html("partial-refund-amount-{$debt->id}", $html);
    }

    /**
     * @di $validator
     */
    public function save(int $debtId, string $amount)
    {
        // Validation
        $values = $this->validator->validateItem(['debt' => $debtId, 'amount' => $amount]);

        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.refund.fund');
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
        $this->cl(Refund::class)->render();
        $this->render();
    }
}
