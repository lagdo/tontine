<?php

namespace Ajax\App\Report\Session\Graph;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

use function trans;

#[Exclude]
class Summary extends Component
{
    use GraphTrait;

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->graphId = 'tontine-graph-session-summary';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card()->options([
            'series' => [
                'labels' => [
                    'show' => true,
                    'position' => 'middle',
                    // 'angle' => 90,
                    // 'labelFormatter' => 'tontine.flot.formatLabel',
                ],
            ],
            'yaxis' => [
                'show' => false,
            ],
            'legend' => [
                'show' => true,
                'noColumns' => 1,
                'container' => "{$this->graphId}-labels",
                // 'labelFormatter' => 'tontine.flot.formatLabel',
            ],
        ]);

        // Set the ticks on X axis.
        // One single point, since the graphs show data only for this session.
        $card->xaxis()->points([[0]]);

        // Add a second graph to the card
        $deposits = $card->graph([
            'label' => trans('meeting.titles.deposits'),
            'bars' => ['show' => true, 'order' => 1],
        ]);
        $deposits->series()->points([[0, $this->stash()->get('report.total.deposits')]]);

        $remitments = $card->graph([
            'label' => trans('meeting.titles.remitments'),
            'bars' => ['show' => true, 'order' => 2],
        ]);
        $remitments->series()->points([[0, -$this->stash()->get('report.total.remitments')]]);

        $settlements = $card->graph([
            'label' => trans('meeting.titles.settlements'),
            'bars' => ['show' => true, 'order' => 3],
        ]);
        $settlements->series()->points([[0, $this->stash()->get('report.total.settlements')]]);

        $outflows = $card->graph([
            'label' => trans('meeting.titles.outflows'),
            'bars' => ['show' => true, 'order' => 4],
        ]);
        $outflows->series()->points([[0, -$this->stash()->get('report.total.outflows')]]);

        $savings = $card->graph([
            'label' => trans('meeting.titles.savings'),
            'bars' => ['show' => true, 'order' => 5],
        ]);
        $savings->series()->points([[0, $this->stash()->get('report.total.savings')]]);

        $loans = $card->graph([
            'label' => trans('meeting.titles.loans'),
            'bars' => ['show' => true, 'order' => 6],
        ]);
        $loans->series()->points([[0, -$this->stash()->get('report.total.loans')]]);

        $refunds = $card->graph([
            'label' => trans('meeting.titles.refunds'),
            'bars' => ['show' => true, 'order' => 7],
        ]);
        $refunds->series()->points([[0, $this->stash()->get('report.total.refunds')]]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
