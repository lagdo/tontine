<?php

namespace Ajax\App\Meeting\Session\Credit\Partial;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag partial.refund
 * @before getFund
 */
class Amount extends Component
{
    /**
     * @var string
     */
    protected $overrides = Refund::class;

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
}
