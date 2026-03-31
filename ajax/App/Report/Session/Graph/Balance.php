<?php

namespace Ajax\App\Report\Session\Graph;

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
        $this->graphId = 'tontine-graph-session-balance';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card()->options($this->pieOptions());

        $session = $this->stash()->get('report.session');
        $inflows = $this->getCounter('deposits', $session->id) +
            $this->getCounter('settlements', $session->id) +
            $this->getCounter('refunds', $session->id) +
            $this->getCounter('savings', $session->id);
        $outflows = $this->getCounter('remitments', $session->id) +
            $this->getCounter('loans', $session->id) +
            $this->getCounter('outflows', $session->id);
        // Add the pie to the card
        $card->pie()->slices([
            [$inflows, trans('meeting.titles.inflows')],
            [$outflows, trans('meeting.titles.outflows')],
        ]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
