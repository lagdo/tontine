<?php

namespace Ajax\App\Planning\Calendar;

use Ajax\App\Page\SectionContent;
use Ajax\Component;
use Siak\Tontine\Service\Planning\RoundService;
use Stringable;

/**
 * @databag planning
 * @before getRound
 */
class Session extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * The constructor
     *
     * @param RoundService $roundService
     */
    public function __construct(private RoundService $roundService)
    {}

    /**
     * @return void
     */
    protected function getRound(): void
    {
        if($this->target()->method() === 'round')
        {
            // Save the round id in the databag.
            $this->bag('planning')->set('round.id', $this->target()->args()[0]);
        }
        $roundId = $this->bag('planning')->get('round.id');
        $this->stash()->set('planning.round', $this->roundService->getRound($roundId));
    }

    public function round(int $roundId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.calendar.session.home', [
            'round' => $this->stash()->get('planning.round'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionPage::class)->page();
    }
}
