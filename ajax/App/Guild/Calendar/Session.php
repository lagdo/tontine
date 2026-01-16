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
            $this->bag('guild.calendar')->set('round.id', $this->target()->args()[0]);
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
