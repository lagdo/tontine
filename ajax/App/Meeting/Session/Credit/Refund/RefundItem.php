<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;

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
    public function html(): string
    {
        $debt = $this->stash()->get('meeting.refund.debt');
        $session = $this->stash()->get('meeting.session');
        $amounts = $this->debtCalculator->getAmounts($debt, $session);

        return $this->renderTpl('pages.meeting.session.refund.item', $amounts);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
