<?php

namespace Ajax\App\Meeting\Summary\Credit\Refund;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Stringable;

/**
 * @exclude
 */
class RefundItem extends Component
{
    /**
     * @var string
     */
    protected string $bagId = 'summary.refund';

    /**
     * @param DebtCalculator $debtCalculator
     */
    public function __construct(private DebtCalculator $debtCalculator)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $debt = $this->stash()->get('summary.refund.debt');
        $session = $this->stash()->get('summary.session');
        $amounts = $this->debtCalculator->getAmounts($debt, $session);

        return $this->renderView('pages.meeting.summary.refund.item', $amounts);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
