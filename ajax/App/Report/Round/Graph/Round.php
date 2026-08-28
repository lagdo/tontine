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
     * @var int
     */
    private int $sessionNum = 0;

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
        $this->graphHeight = '220px';
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $lastSession = $this->stash()->get('report.session');
        $sessions = $this->stash()->get('report.sessions');
        $sessions = $sessions
            ->filter(fn($session) => $session->day_date <= $lastSession->day_date)
            // Sort the sessions by date.
            ->sortBy(fn($session) => $session->day_date);

        $card = $this->card()->options($this->lineOptions());
        // Set the session dates as ticks on X axis.
        $dateFormat = trans('tontine.date.format_md');
        $this->sessionNum = 0;
        $card->xaxis()->options(['labelHeight' => 16, 'position' => 'bottom'])
            ->points($sessions->map(fn($session) =>
                [$this->sessionNum++, $session->day_date->format($dateFormat)])->toArray());
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
            $this->sessionNum = 0;
            $graph->series()->points($sessions->map(function($session) use($counter) {
                $value = $this->getCounter($counter, $session->id);
                return [$this->sessionNum++, $value, $this->localeService->formatMoney($value)];
            })->toArray());
        }

        // Draw the graph
        $this->flot()->draw($card);
    }
}
