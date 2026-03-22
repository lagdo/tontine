<?php

namespace Ajax\App\Report\Session\Graph;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

use function trans;

#[Exclude]
class Inflow extends Component
{
    use GraphTrait;

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->graphId = 'tontine-graph-session-inflow';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card();

        // Set the card options
        $card->options($this->pieOptions());

        // Add the pie to the card
        $card->pie()->slices([
            [$this->stash()->get('report.total.deposits'), trans('meeting.titles.deposits')],
            [$this->stash()->get('report.total.settlements'), trans('meeting.titles.settlements')],
            [$this->stash()->get('report.total.refunds'), trans('meeting.titles.refunds')],
            [$this->stash()->get('report.total.savings'), trans('meeting.titles.savings')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
