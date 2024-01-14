<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

use function Jaxon\jq;
use function trans;

/**
 * @databag presence
 * @before getSession
 */
class Member extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @param SessionService $sessionService
     * @param MemberService $memberService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private MemberService $memberService, private PresenceService $presenceService)
    {}

    protected function getSession()
    {
        $sessionId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('presence')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home($session->id);
    }

    public function home(int $sessionId)
    {
        $this->bag('presence')->set('session.id', $this->session->id);
        $this->bag('presence')->set('member.page', 1);

        $html = $this->render('pages.meeting.presence.member.home', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home-members', $html);

        $this->jq('#btn-presence-members-refresh')->click($this->rq()->page());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('presence')->get('member.search', ''));
        $memberCount = $this->presenceService->getMemberCount($search);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'presence', 'member.page');
        $members = $this->presenceService->getMembers($search, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);
        $absences = $this->presenceService->getSessionAbsences($this->session);

        $html = $this->render('pages.meeting.presence.member.page', [
            'session' => $this->session,
            'search' => $search,
            'members' => $members,
            'absences' => $absences,
            'pagination' => $pagination,
            'sessionCount' => $this->presenceService->getActiveSessionCount(),
        ]);
        $this->response->html('content-page-members', $html);

        $this->jq('#btn-presence-members-search')
            ->click($this->rq()->search(jq('#txt-presence-members-search')->val()));
        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-toggle-member-presence')->click($this->rq()->togglePresence($memberId));

        return $this->response;
    }

    public function search(string $search)
    {
        $this->bag('presence')->set('member.search', trim($search));

        return $this->page();
    }

    // public function toggleFilter()
    // {
    //     $filter = $this->bag('presence')->get('member.filter', null);
    //     // Switch between null, true and false
    //     $filter = $filter === null ? true : ($filter === true ? false : null);
    //     $this->bag('presence')->set('member.filter', $filter);

    //     return $this->page();
    // }

    public function togglePresence(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        if(!$member)
        {
            return $this->response;
        }

        $this->presenceService->togglePresence($this->session, $member);

        $this->cl(Session::class)->page();
        return $this->page();
    }
}
