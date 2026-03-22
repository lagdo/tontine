<?php

namespace Ajax\App\Report\Session\Graph;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

use function trans;

#[Exclude]
class Outflow extends Component
{
    use GraphTrait;

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->graphId = 'tontine-graph-session-outflow';
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
            [$this->stash()->get('report.total.remitments'), trans('meeting.titles.remitments')],
            [$this->stash()->get('report.total.loans'), trans('meeting.titles.loans')],
            [$this->stash()->get('report.total.outflows'), trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
