<?php

namespace Ajax\App\Meeting\Session\Credit;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

use function trans;

/**
 * @databag refund
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
     * @param RefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected RefundService $refundService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.refund.home', [
            'session' => $session,
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

    protected function getFund()
    {
        // Try to get the selected savings fund.
        $fund = null;
        $fundId = $this->bag('refund')->get('fund.id', 0);
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
            $this->bag('refund')->set('fund.id', $fund->id);
        }
        $this->stash()->set('meeting.refund.fund', $fund);
    }

    /**
     * @param int $fundId
     *
     * @return void
     */
    public function fund(int $fundId)
    {
        $this->bag('refund')->set('fund.id', $fundId);
        $this->bag('refund')->set('principal.page', 1);
        $this->getFund();

        $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('filter', $filtered);
        $this->bag('refund')->set('principal.page', 1);

        $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
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

        $this->cl(Partial\RefundPage::class)->render();
        $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
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

        $this->cl(Partial\RefundPage::class)->render();
        $this->cl(RefundPage::class)->page();
    }
}
