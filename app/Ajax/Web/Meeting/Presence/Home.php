<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\PresenceService;

use function trans;

/**
 * @databag presence
 * @before checkGuestAccess ["meeting", "presences"]
 */
class Home extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    /**
     * @exclude
     */
    public function getSession(): ?SessionModel
    {
        if(($sessionId = $this->bag('presence')->get('session.id', 0)) === 0)
        {
            return null;
        }
        return $this->presenceService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function getMember(): ?MemberModel
    {
        if(($memberId = $this->bag('presence')->get('member.id', 0)) === 0)
        {
            return null;
        }
        return $this->presenceService->getMember($memberId);
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
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.presence.home', [
            'exchange' => $this->bag('presence')->get('exchange', false),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $clAtLeft = !$exchange ? $this->cl(Session::class) : $this->cl(Member::class);
        $clAtLeft->render();
    }

    public function exchange()
    {
        $exchange = $this->bag('presence')->get('exchange', false);
        $this->bag('presence')->set('exchange', !$exchange);

        return $this->home();
    }

    public function selectSession(int $sessionId)
    {
        $this->bag('presence')->set('session.id', $sessionId);
        $this->bag('presence')->set('member.id', 0);
        $this->bag('presence')->set('member.page', 1);

        return $this->cl(Member::class)->render();
    }

    public function selectMember(int $memberId)
    {
        $this->bag('presence')->set('member.id', $memberId);
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('session.page', 1);

        return $this->cl(Session::class)->render();
    }
}
