<?php

namespace App\Ajax\Web\Report;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

/**
 * @databag report
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
     * @before checkGuestAccess ["report", "session"]
     * @before checkOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.report'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));

        return (string)$this->renderView('pages.report.session.home', [
            'sessionId' => $sessions->count() > 0 ? $sessions->first()->id : 0,
            'sessions' => $sessions->pluck('title', 'id'),
            'members' => $this->memberService->getMemberList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function after()
    {
        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        if($sessions->count() > 0)
        {
            $this->cl(SessionContent::class)->show($sessions->first());
        }
    }

    public function showSession(int $sessionId)
    {
        if($sessionId <= 0 || !($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }

        return $this->cl(SessionContent::class)->show($session);
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if($sessionId <= 0 || $memberId <= 0 ||
            !($session = $this->sessionService->getSession($sessionId)) ||
            !($member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }

        return $this->cl(SessionContent::class)->show($session, $member);
    }
}
