<?php

namespace Ajax\App\Report\Round\Graph;

use Ajax\Base\Round\Component;
use Ajax\App\Report\Graph\GraphTrait;
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
        $this->graphId = 'tontine-graph-round-inflow';
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
            [$this->getCounterSum('deposits'), trans('meeting.titles.deposits')],
            [$this->getCounterSum('settlements'), trans('meeting.titles.settlements')],
            [$this->getCounterSum('refunds'), trans('meeting.titles.refunds')],
            [$this->getCounterSum('savings'), trans('meeting.titles.savings')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
