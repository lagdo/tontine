<?php

namespace Ajax\App\Meeting\Session\Refund\Total;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Ajax\App\Meeting\Session\Refund\Partial\RefundPage as PartialRefundPage;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function trans;

/**
 * @databag meeting.refund.final
 * @before getFund
 */
class RefundFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.final';

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

    public function toggleFilter()
    {
        $filtered = $this->bag($this->bagId)->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag($this->bagId)->set('filter', $filtered);
        $this->bag($this->bagId)->set('page', 1);

        $this->cl(RefundPage::class)->page();
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

        $this->cl(RefundPage::class)->page();
        $this->stash()->set('meeting.refund.partial.fund',
            $this->stash()->get('meeting.refund.final.fund'));
        $this->cl(PartialRefundPage::class)->page();
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

        $this->cl(RefundPage::class)->page();
        $this->stash()->set('meeting.refund.partial.fund',
            $this->stash()->get('meeting.refund.final.fund'));
        $this->cl(PartialRefundPage::class)->page();
    }
}
