<?php

namespace Ajax\App\Report\Round\Graph;

use Ajax\Base\Round\Component;
use Ajax\App\Report\Graph\GraphTrait;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\LocaleService;

use function trans;

#[Exclude]
class Total extends Component
{
    use GraphTrait;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(protected LocaleService $localeService)
    {}

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->graphId = 'tontine-graph-round-total';
        $this->graphHeight = '200px';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $options = $this->barOptions();
        $options['legend']['labelFormatter'] = 'tontine.flot.totalLabel';
        $card = $this->card()->options($options);
        // Set the ticks on X axis.
        // One single point, since the graphs show data only for this session.
        $card->xaxis()->points([[0]]);

        $labels = [];
        foreach($this->counters() as $counter => ['order' => $order, 'coef' => $coef])
        {
            $value = $this->getCounterSum($counter);
            $labels[$counter] = trans("meeting.titles.$counter") .
                ': ' . $this->localeService->formatMoney($value);

            // Add a graph to the card
            $graph = $card->graph([
                'label' => $counter,
                'bars' => ['show' => true, 'horizontal' => true, 'order' => $order],
            ]);
            $graph->series()->points([[0, $coef * $value]]);
        }

        // Save the labels in the js object.
        // The js "tontine.flot.totalLabel" function reads the labels here.
        $this->response()->jo('tontine.labels')->total = $labels;

        // Draw the graph
        $this->flot()->draw($card);
    }
}
