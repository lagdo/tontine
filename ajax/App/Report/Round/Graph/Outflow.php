<?php

namespace Ajax\App\Report\Round\Graph;

use Ajax\Base\Round\Component;
use Ajax\App\Report\Graph\GraphTrait;
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
        $this->graphId = 'tontine-graph-round-outflow';
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
            [$this->getCounterSum('remitments'), trans('meeting.titles.remitments')],
            [$this->getCounterSum('loans'), trans('meeting.titles.loans')],
            [$this->getCounterSum('outflows'), trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
