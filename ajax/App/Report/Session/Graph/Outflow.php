<?php

namespace Ajax\App\Report\Session\Graph;

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
        $this->graphId = 'tontine-graph-session-outflow';
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
            [$this->getCounter('remitments', $session->id), trans('meeting.titles.remitments')],
            [$this->getCounter('loans', $session->id), trans('meeting.titles.loans')],
            [$this->getCounter('outflows', $session->id), trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
