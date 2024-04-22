<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Presence;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Tontine\MemberService;

use function Jaxon\jq;

/**
 * @databag presence
 * @before getSession
 */
class Member extends CallableClass
{
    /**
     * @var bool
     */
    private $fromHome = false;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @param MemberService $memberService
     * @param PresenceService $presenceService
     */
    public function __construct(private MemberService $memberService,
        private PresenceService $presenceService)
    {}

    protected function getSession()
    {
        if($this->target()->method() === 'home' ||
            ($sessionId = $this->bag('presence')->get('session.id')) === 0)
        {
            return;
        }
        $this->session = $this->presenceService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;
        $this->bag('presence')->set('session.id', $session->id);
        $this->bag('presence')->set('member.page', 1);

        return $this->_home();
    }

    public function home()
    {
        $this->fromHome = true;
        $this->bag('presence')->set('session.id', 0);
        $this->bag('presence')->set('member.page', 1);

        return $this->_home();
    }

    private function _home()
    {
        $search = trim($this->bag('presence')->get('member.search', ''));
        $html = $this->render('pages.meeting.presence.member.home', [
            'session' => $this->session, // Is null when showing presences by members.
            'memberCount' => $this->presenceService->getMemberCount($search),
        ]);
        $this->response->html('content-home-members', $html);

        $this->jq('#btn-presence-members-refresh')->click($this->rq()->page());
        $this->jq('#btn-presence-members-search')
            ->click($this->rq()->search(jq('#txt-presence-members-search')->val()));

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
        $absences = !$this->session ? null :
            $this->presenceService->getSessionAbsences($this->session);

        $html = $this->render('pages.meeting.presence.member.page', [
            'session' => $this->session, // Is null when showing presences by members.
            'search' => $search,
            'members' => $members,
            'absences' => $absences,
            'pagination' => $pagination,
            'sessionCount' => $this->presenceService->getSessionCount(),
        ]);
        $this->response->html('content-page-members', $html);
        $this->response->call('makeTableResponsive', 'content-page-members');

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-toggle-member-presence')->click($this->rq()->togglePresence($memberId));
        $this->jq('.btn-show-member-presences')
            ->click($this->rq(Presence::class)->selectMember($memberId));

        if($this->fromHome && $members->count() > 0)
        {
            $member = $members->first();
            $this->cl(Session::class)->show($member);
        }

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
        // Refresh the session counters
        $this->session = $this->presenceService->getSession($this->session->id);

        $this->cl(Session::class)->page();
        return $this->page();
    }
}
