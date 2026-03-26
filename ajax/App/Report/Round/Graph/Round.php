<?php

namespace Ajax\App\Report\Round\Graph;

use Ajax\Base\Round\Component;
use Ajax\App\Report\Graph\GraphTrait;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\LocaleService;

use function trans;

#[Exclude]
class Round extends Component
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
        $this->graphId = 'tontine-graph-round-current';
        $this->graphHeight = '230px';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $sessions = $this->stash()->get('report.sessions');
        if($sessions->count() < 2)
        {
            return;
        }

        $card = $this->card()->options($this->lineOptions());
        // Set the sessions as ticks on X axis.
        $card->xaxis()->points($sessions->map(fn($session) => [$session->id, $session->day_date])->toArray());
        // $card->yaxis()->options([
        //     'position' => 'right',
        //     'tickFormatter' => 'tontine.flot.formatTickY',
        // ]);

        foreach($this->counters() as $counter => $_)
        {
            // Add a graph to the card
            $graph = $card->graph([
                'label' => trans("meeting.titles.$counter"),
                'lines' => ['show' => true],
                'points' => ['show' => true],
            ]);
            $graph->series()->points($sessions->map(function($session) use($counter) {
                $value = $this->getCounter($counter, $session->id);
                return [$session->id, $value, $this->localeService->formatMoney($value)];
            })->toArray());
        }

        // Draw the graph
        $this->flot()->draw($card);
    }
}
