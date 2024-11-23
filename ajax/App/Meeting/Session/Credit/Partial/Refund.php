<?php

namespace Ajax\App\Meeting\Session\Credit\Partial;

use Ajax\App\Meeting\MeetingComponent;
use Ajax\App\Meeting\Session\Credit\Refund as CreditRefund;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

use function Jaxon\pm;
use function trans;

/**
 * @databag partial.refund
 */
class Refund extends MeetingComponent
{
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
        $fund = null;
        $fundId = $this->bag('partial.refund')->get('fund.id', 0);
        if($fundId !== 0)
        {
            if(($fund = $this->fundService->getFund($fundId, true)) === null)
            {
                $fundId = 0;
            }
        }
        if($fundId === 0)
        {
            // If not found, then revert to the tontine default fund.
            $fund = $this->fundService->getDefaultFund();
            $this->bag('partial.refund')->set('fund.id', $fund->id);
        }
        $this->cache->set('meeting.refund.fund', $fund);
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.refund.partial.home', [
            'session' => $this->cache->get('meeting.session'),
            'funds' => $this->fundService->getFundList(),
            'currentFundId' => $this->bag('partial.refund')->get('fund.id', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund($this->bag('partial.refund')->get('fund.id', 0));
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('partial.refund')->set('fund.id', $fundId);
        $this->bag('partial.refund')->set('principal.page', 1);
        $this->getFund();

        return $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function edit(int $refundId)
    {
        $session = $this->cache->get('meeting.session');
        $refund = $this->refundService->getPartialRefund($session, $refundId);
        $title = trans('meeting.refund.titles.edit');
        $content = $this->renderView('pages.meeting.refund.partial.edit', [
            'session' => $session,
            'refund' => $refund,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($refundId, pm()->form('refund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @before getFund
     * @di $validator
     */
    public function update(int $refundId, array $formValues)
    {
        $session = $this->cache->get('meeting.session');
        $formValues['debt'] = $refundId;
        $values = $this->validator->validateItem($formValues);
        $refund = $this->refundService->getPartialRefund($session, $refundId);

        $this->refundService->updatePartialRefund($refund, $session, $values['amount']);

        $this->dialog->hide();

        // Refresh the refunds page
        $this->cl(CreditRefund::class)->render();

        return $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function delete(int $refundId)
    {
        $session = $this->cache->get('meeting.session');
        $refund = $this->refundService->getPartialRefund($session, $refundId);
        $this->refundService->deletePartialRefund($refund, $session);

        // Refresh the refunds page
        $this->cl(CreditRefund::class)->render();

        return $this->cl(RefundPage::class)->page();
    }
}
