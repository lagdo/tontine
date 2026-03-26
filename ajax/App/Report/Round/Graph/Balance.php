<?php

namespace Ajax\App\Report\Round\Graph;

use Ajax\Base\Round\Component;
use Ajax\App\Report\Graph\GraphTrait;
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
        $this->graphId = 'tontine-graph-round-balance';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card();

        // Set the card options
        $card->options($this->pieOptions());

        $inflows = $this->getCounterSum('deposits') +
            $this->getCounterSum('settlements') +
            $this->getCounterSum('refunds') +
            $this->getCounterSum('savings');
        $outflows = $this->getCounterSum('remitments') +
            $this->getCounterSum('loans') +
            $this->getCounterSum('outflows');
        // Add the pie to the card
        $card->pie()->slices([
            [$inflows, trans('meeting.titles.inflows')],
            [$outflows, trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
