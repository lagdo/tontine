<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Stringable;

#[Exclude]
class RefundItem extends Component
{
    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund';

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
        $debt = $this->stash()->get('meeting.refund.debt');
        $session = $this->stash()->get('meeting.session');
        $amounts = $this->debtCalculator->getAmounts($debt, $session);

        return $this->renderView('pages.meeting.session.refund.item', $amounts);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
