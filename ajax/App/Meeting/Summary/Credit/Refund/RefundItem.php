<?php

namespace Ajax\App\Meeting\Summary\Credit\Refund;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Stringable;

#[Exclude]
class RefundItem extends Component
{
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
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
