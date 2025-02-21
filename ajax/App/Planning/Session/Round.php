<?php

namespace Ajax\App\Planning\Session;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag planning
 */
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["planning", "sessions"]
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(RoundPage::class)->page();

        // Show the session of the default round.
        $round = $this->tenantService->round();
        if($round !== null)
        {
            $this->bag('planning')->set('round.id', $round->id);
            $this->stash()->set('planning.round', $round);
            $this->cl(Session::class)->render();
        }

        $this->response->js('Tontine')->showSmScreen('content-planning-rounds', 'round-sm-screens');
    }
}
