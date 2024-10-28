<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag partial.refund
 */
class PartialRefund extends MeetingComponent
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
        Cache::set('meeting.refund.partial.fund', $fund);
    }

    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.refund.partial.home', [
            'session' => Cache::get('meeting.session'),
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund(0);
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

        return $this->cl(PartialRefundPage::class)->page();
    }

    public function editRefund(int $refundId)
    {
        $session = Cache::get('meeting.session');
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
            'click' => $this->rq()->updateRefund($refundId, pm()->form('refund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @before getFund
     * @di $validator
     */
    public function updateRefund(int $refundId, array $formValues)
    {
        $session = Cache::get('meeting.session');
        $formValues['debt'] = $refundId;
        $values = $this->validator->validateItem($formValues);
        $refund = $this->refundService->getPartialRefund($session, $refundId);

        $this->refundService->updatePartialRefund($refund, $session, $values['amount']);

        $this->dialog->hide();

        // Refresh the refunds page
        $this->cl(Refund::class)->render();

        return $this->cl(PartialRefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function deleteRefund(int $refundId)
    {
        $session = Cache::get('meeting.session');
        $refund = $this->refundService->getPartialRefund($session, $refundId);
        $this->refundService->deletePartialRefund($refund, $session);

        // Refresh the refunds page
        $this->cl(Refund::class)->render();

        return $this->cl(PartialRefundPage::class)->page();
    }
}
