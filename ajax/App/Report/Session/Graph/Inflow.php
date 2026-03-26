<?php

namespace Ajax\App\Report\Session\Graph;

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
        $this->graphId = 'tontine-graph-session-inflow';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card()->options($this->pieOptions());

        // Add the pie to the card
        $session = $this->stash()->get('report.session');
        $card->pie()->slices([
            [$this->getCounter('deposits', $session->id), trans('meeting.titles.deposits')],
            [$this->getCounter('settlements', $session->id), trans('meeting.titles.settlements')],
            [$this->getCounter('refunds', $session->id), trans('meeting.titles.refunds')],
            [$this->getCounter('savings', $session->id), trans('meeting.titles.savings')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
