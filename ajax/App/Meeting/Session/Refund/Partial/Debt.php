<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Stringable;

/**
 * @databag meeting.refund.partial
 * @before getFund
 */
class Debt extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.partial';

    /**
     * @var string
     */
    protected $overrides = Refund::class;

    /**
     * The constructor
     *
     * @param PartialRefundService $refundService
     */
    public function __construct(protected PartialRefundService $refundService)
    {}

    public function fund(int $fundId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->renderView('pages.meeting.refund.partial.debt.page', [
            'session' => $session,
            'fund' => $fund,
            'debts' => $this->refundService->getUnpaidDebts($fund, $session),
        ]);
    }
}
