<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\Web\Meeting\MeetingComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jaxon;

/**
 * @databag partial.refund
 * @before getFund
 */
class PartialRefundEdit extends MeetingComponent
{
    /**
     * @var string
     */
    protected $overrides = PartialRefund::class;

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
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
        $fund = null;
        $fundId = $this->bag('partial.refund')->get('fund.id', 0);
        if($fundId !== 0 && ($fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $fund = $this->fundService->getDefaultFund();
            $this->bag('partial.refund')->set('fund.id', $fund->id);
        }
        $this->cache->set('meeting.refund.partial.fund', $fund);
    }

    public function html(): string
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.refund.partial.fund');

        return (string)$this->renderView('pages.meeting.refund.partial.edit-list', [
            'session' => $session,
            'debts' => $this->refundService->getUnpaidDebts($fund, $session),
        ]);
    }

    /**
     * @di $localeService
     */
    public function editAmount(int $debtId)
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.refund.partial.fund');
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $html = $this->renderView('pages.meeting.refund.partial.amount.edit', [
            'debt' => $debt,
            'amount' => $this->localeService->getMoneyValue($debt->partial_refund->amount),
        ]);
        jaxon()->getResponse()->html("partial-refund-amount-{$debt->id}", $html);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveAmount(int $debtId, int $amount)
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.refund.partial.fund');
        $values = $this->validator->validateItem(['debt' => $debtId, 'amount' => $amount]);
        $debt = $this->refundService->getUnpaidDebt($fund, $session, $debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $this->refundService->savePartialRefund($debt, $session, $values['amount']);

        $this->dialog->hide();

        // Refresh the refunds page
        $this->cl(Refund::class)->render();

        return $this->render();
    }
}
