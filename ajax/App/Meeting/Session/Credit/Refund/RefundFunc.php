<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
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

        $fund = $this->getStashedFund();
        $debt = $this->refundService->getFundDebt($session, $fund, $debtId);
        $this->stash()->set('meeting.refund.debt', $debt);
        $this->cl(RefundItem::class)->item($debt->id)->render();
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

        $fund = $this->getStashedFund();
        $debt = $this->refundService->getFundDebt($session, $fund, $debtId);
        $this->stash()->set('meeting.refund.debt', $debt);
        $this->cl(RefundItem::class)->item($debt->id)->render();
    }
}
