<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

#[Before('checkHostAccess', ["report", "session"])]
#[Before('checkOpenedSessions')]
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

    #[Before('setSectionTitle', ["report", "session"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        return $this->renderView('pages.report.session.home', [
            'session' => $sessions->first(),
            'sessions' => $sessions->pluck('title', 'id'),
            'members' => $this->memberService->getMemberList($this->round())->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
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
        $this->stash()->set('report.session', $session);
        $this->stash()->set('report.member', $member);

        // Render the page header.
        $this->cl(ReportHeader::class)->render();

        $this->cl(SessionContent::class)->render();
    }

    public function showSession(int $sessionId)
    {
        if($sessionId <= 0 ||
            !($session = $this->sessionService->getSession($this->round(), $sessionId)))
        {
            return;
        }
        $this->renderContent($session);
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if($sessionId <= 0 || $memberId <= 0 ||
            !($session = $this->sessionService->getSession($this->round(), $sessionId)) ||
            !($member = $this->memberService->getMember($this->round(), $memberId)))
        {
            return;
        }
        $this->renderContent($session, $member);
    }
}
