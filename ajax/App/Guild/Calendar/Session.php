<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Service\Guild\RoundService;
use Stringable;

/**
 * @databag planning.calendar
 * @before checkHostAccess ["guild", "calendar"]
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
            $this->bag('planning.calendar')->set('round.id', $this->target()->args()[0]);
        }
        $roundId = $this->bag('planning.calendar')->get('round.id');
        $this->stash()->set('planning.calendar.round', $this->roundService->getRound($roundId));
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
        return $this->renderView('pages.guild.calendar.session.home', [
            'round' => $this->stash()->get('planning.calendar.round'),
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
