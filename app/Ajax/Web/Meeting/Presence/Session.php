<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;
use function Jaxon\rq;

/**
 * @databag presence
 * @before getMember
 */
class Session extends CallableClass
{
    /**
     * @var MemberModel|null
     */
    protected ?MemberModel $member = null;

    /**
     * @param SessionService $sessionService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    protected function getMember()
    {
        if($this->target()->method() === 'home' ||
            ($memberId = $this->bag('presence')->get('member.id')) === 0)
        {
            return;
        }
        $this->member = $this->presenceService->getMember($memberId);
    }

    /**
     * @exclude
     */
    public function show(MemberModel $member)
    {
        $this->member = $member;
        $this->bag('presence')->set('member.id', $this->member->id);
        $this->bag('presence')->set('session.page', 1);

        $this->_home();

        $this->response->call('showSmScreen', 'content-home-sessions', 'presence-sm-screens');
        $this->jq('#btn-presence-members-back')->click(rq('.')
            ->showSmScreen('content-home-members', 'presence-sm-screens'));

        // if($sessions->count() > 0)
        // {
        //     $session = $sessions->first();
        //     $this->cl(Member::class)->show($session);
        // }

        return $this->response;
    }

    public function home()
    {
        $this->bag('presence')->set('member.id', 0);
        $this->bag('presence')->set('session.page', 1);

        return $this->_home();
    }

    private function _home()
    {
        $html = $this->renderView('pages.meeting.presence.session.home', [
            'member' => $this->member, // Is null when showing presences by sessions.
            'sessionCount' => $this->presenceService->getSessionCount(),
        ]);
        $this->response->html('content-home-sessions', $html);

        if(!$this->member)
        {
            $this->jq('#btn-presence-exchange')->click($this->rq(Home::class)->exchange());
        }

        $this->jq('#btn-presence-sessions-refresh')->click($this->rq()->page());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->presenceService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'presence', 'session.page');
        $sessions = $this->presenceService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);
        $absences = !$this->member ? null :
            $this->presenceService->getMemberAbsences($this->member);

        $html = $this->renderView('pages.meeting.presence.session.page', [
            'member' => $this->member, // Is null when showing presences by sessions.
            'sessions' => $sessions,
            'absences' => $absences,
            'pagination' => $pagination,
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount(),
        ]);
        $this->response->html('content-page-sessions', $html);
        $this->response->call('makeTableResponsive', 'content-page-sessions');

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-toggle-session-presence')->click($this->rq()->togglePresence($sessionId));
        $this->jq('.btn-show-session-presences')
            ->click($this->rq(Home::class)->selectSession($sessionId));

        return $this->response;
    }

    public function togglePresence(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        if(!$session)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($session, $this->member);
        // Refresh the member counters
        $this->member = $this->presenceService->getMember($this->member->id);

        $this->cl(Member::class)->page();
        return $this->page();
    }
}
