<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Base\Guild\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Guild\RoundService;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Before('getRound')]
#[Databag('guild.calendar')]
#[Export(base: ['render'])]
class Session extends Component
{
    /**
     * The constructor
     *
     * @param RoundService $roundService
     */
    public function __construct(private RoundService $roundService)
    {}

    /**
     * @return string
     */
    protected function overrides(): string
    {
        return SectionContent::class;
    }

    /**
     * @return void
     */
    protected function getRound(): void
    {
        if($this->action()->func() === 'round')
        {
            // Save the round id in the databag.
            $this->bag('guild.calendar')->set('round.id', $this->action()->args()[0]);
        }
        $roundId = $this->bag('guild.calendar')->get('round.id');
        $round = $this->roundService->getRound($this->guild(), $roundId);
        $this->stash()->set('guild.calendar.round', $round);
    }

    public function round(int $roundId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.calendar.session.home', [
            'round' => $this->stash()->get('guild.calendar.round'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionPage::class)->page();
    }
}
