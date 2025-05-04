<?php

namespace Ajax\App\Meeting\Presence;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag presence
 * @before checkHostAccess ["meeting", "presences"]
 */
class Presence extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkRoundSessions
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.presences'));
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('member.id', 0);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.presence.home', [
            'exchange' => $this->bag('presence')->get('exchange', false),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        !$exchange ?
            $this->cl(Session::class)->render() :
            $this->cl(Member::class)->render();
        $this->response->js('Tontine')
            ->showSmScreen('content-presence-left', 'presence-sm-screens');
    }

    public function exchange()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $this->bag('presence')->set('exchange', !$exchange);

        $this->render();
    }
}
