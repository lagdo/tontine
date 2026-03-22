<?php

namespace Ajax\App\Report\Session\Graph;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

use function trans;

#[Exclude]
class Balance extends Component
{
    use GraphTrait;

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->graphId = 'tontine-graph-session-balance';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card();

        // Set the card options
        $card->options($this->pieOptions());

        $inflows = $this->stash()->get('report.total.deposits') +
            $this->stash()->get('report.total.settlements') +
            $this->stash()->get('report.total.refunds') +
            $this->stash()->get('report.total.savings');
        $outflows = $this->stash()->get('report.total.remitments') +
            $this->stash()->get('report.total.loans') +
            $this->stash()->get('report.total.outflows');
        // Add the pie to the card
        $card->pie()->slices([
            [$inflows, trans('meeting.titles.inflows')],
            [$outflows, trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
