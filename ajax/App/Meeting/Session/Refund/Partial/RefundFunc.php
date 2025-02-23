<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\Refund\FundTrait;
use Ajax\App\Meeting\Session\Refund\Total\Refund as TotalRefund;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag partial.refund
 * @before getFund
 */
class RefundFunc extends FuncComponent
{
    use FundTrait;

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

    public function edit(int $refundId)
    {
        $session = $this->stash()->get('meeting.session');
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
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function update(int $refundId, array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        $formValues['debt'] = $refundId;
        $values = $this->validator->validateItem($formValues);
        $refund = $this->refundService->getPartialRefund($session, $refundId);

        $this->refundService->updatePartialRefund($refund, $session, $values['amount']);

        $this->modal()->hide();
        // Refresh the refunds page
        $this->cl(TotalRefund::class)->render();
        $this->cl(RefundPage::class)->page();
    }

    public function delete(int $refundId)
    {
        $session = $this->stash()->get('meeting.session');
        $refund = $this->refundService->getPartialRefund($session, $refundId);
        $this->refundService->deletePartialRefund($refund, $session);

        // Refresh the refunds page
        $this->cl(TotalRefund::class)->render();
        $this->cl(RefundPage::class)->page();
    }
}
