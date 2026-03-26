<?php

namespace Ajax\App\Report\Round\Graph;

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
        $this->graphId = 'tontine-graph-round-summary';
        $this->graphHeight = '200px';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $card = $this->card()->options([
            'series' => [
                'labels' => [
                    'show' => false,
                    // 'position' => 'middle',
                    // 'angle' => 90,
                    // 'labelFormatter' => 'tontine.flot.formatLabel',
                ],
            ],
            'yaxis' => [
                'show' => false,
            ],
            'legend' => [
                'show' => true,
                'noColumns' => 2,
                'container' => "{$this->graphId}-labels",
                'labelFormatter' => 'tontine.flot.formatLabel',
            ],
        ]);

        // Set the ticks on X axis.
        // One single point, since the graphs show data only for this session.
        $card->xaxis()->points([[0]]);

        // Add a second graph to the card
        $deposits = $card->graph([
            'label' => trans('meeting.titles.deposits'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 7],
        ]);
        $deposits->series()->points([[0, $this->stash()->get('report.total.deposits')]]);

        $remitments = $card->graph([
            'label' => trans('meeting.titles.remitments'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 6],
        ]);
        $remitments->series()->points([[0, -$this->stash()->get('report.total.remitments')]]);

        $settlements = $card->graph([
            'label' => trans('meeting.titles.settlements'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 5],
        ]);
        $settlements->series()->points([[0, $this->stash()->get('report.total.settlements')]]);

        $outflows = $card->graph([
            'label' => trans('meeting.titles.outflows'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 4],
        ]);
        $outflows->series()->points([[0, -$this->stash()->get('report.total.outflows')]]);

        $savings = $card->graph([
            'label' => trans('meeting.titles.savings'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 3],
        ]);
        $savings->series()->points([[0, $this->stash()->get('report.total.savings')]]);

        $loans = $card->graph([
            'label' => trans('meeting.titles.loans'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 2],
        ]);
        $loans->series()->points([[0, -$this->stash()->get('report.total.loans')]]);

        $refunds = $card->graph([
            'label' => trans('meeting.titles.refunds'),
            'bars' => ['show' => true, 'horizontal' => true, 'order' => 1],
        ]);
        $refunds->series()->points([[0, $this->stash()->get('report.total.refunds')]]);

        // Draw the graph
        $this->flot()->draw($card);
    }
}
