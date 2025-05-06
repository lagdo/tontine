<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

/**
 * @databag report
 * @before checkHostAccess ["report", "session"]
 * @before checkOpenedSessions
 */
class Session extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(protected MemberService $memberService,
        protected SessionService $sessionService)
    {}

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.report'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));

        return $this->renderView('pages.report.session.home', [
            'sessionId' => $sessions->count() > 0 ? $sessions->first()->id : 0,
            'sessions' => $sessions->pluck('title', 'id'),
            'members' => $this->memberService->getMemberList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        if($sessions->count() > 0)
        {
            $this->renderContent($sessions->first());
        }
    }

    /**
     * @param SessionModel $session
     * @param MemberModel|null $member
     *
     * @return void
     */
    private function renderContent(SessionModel $session, ?MemberModel $member = null): void
    {
        $this->bag('report')->set('session.id', $session->id);
        $this->stash()->set('report.session', $session);
        $this->stash()->set('report.member', $member);

        $this->cl(SessionContent::class)->render();
    }

    public function showSession(int $sessionId)
    {
        if($sessionId <= 0 || !($session = $this->sessionService->getSession($sessionId)))
        {
            return;
        }

        $this->renderContent($session);
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if($sessionId <= 0 || $memberId <= 0 ||
            !($session = $this->sessionService->getSession($sessionId)) ||
            !($member = $this->memberService->getMember($memberId)))
        {
            return;
        }

        $this->renderContent($session, $member);
    }
}
