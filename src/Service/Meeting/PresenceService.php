<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\TenantService;

class PresenceService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService,
        private MemberService $memberService, private SessionService $sessionService)
    {}

    /**
     * @param Round $round
     *
     * @return Collection
     */
    public function getRoundAbsences(Round $round): Collection
    {
        return Member::select('members.id', 'members.name', 'absences.session_id')
            ->join('absences', 'absences.member_id', '=', 'members.id')
            ->join('sessions', 'absences.session_id', '=', 'sessions.id')
            ->where('sessions.round_id', $round->id)
            ->get()
            // Group the data (member name) by session id and member id.
            ->groupBy('session_id')
            ->map(fn($members) => $members->groupBy('id')
                ->map(fn($_members) => $_members->first()->name));
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionAbsences(Session $session): Collection
    {
        return $session->absents()->pluck('name', 'id');
    }

    /**
     * @param Member $member
     *
     * @return Collection
     */
    public function getMemberAbsences(Member $member): Collection
    {
        $round = $this->tenantService->round();
        return $member->absences()->where('round_id', $round->id)->pluck('title', 'id');
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return void
     */
    public function togglePresence(Session $session, Member $member)
    {
        !$session->absents()->find($member->id) ?
            $session->absents()->attach($member->id) :
            $session->absents()->detach($member->id);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @return int
     */
    public function getSessionCount(): int
    {
        return $this->sessionService->active()->getSessionCount();
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getSessions(int $page = 0): Collection
    {
        return $this->sessionService->active()
            ->withCount(['absents'])
            ->getSessions($page, true);
    }

    /**
     * Find a session.
     *
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->sessionService->active()
            ->withCount(['absents'])
            ->getSession($sessionId);
    }

    /**
     * Get the number of members.
     *
     * @param string $search
     *
     * @return int
     */
    public function getMemberCount(string $search = ''): int
    {
        return $this->memberService->active()->getMemberCount($search);
    }

    /**
     * Get a paginated list of members.
     *
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(string $search, int $page = 0): Collection
    {
        $round = $this->tenantService->round();
        return $this->memberService->active()
            ->withCount(['absences' => fn($query) => $query->where('round_id', $round->id)])
            ->getMembers($search, $page);
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        $round = $this->tenantService->round();
        return $this->memberService->active()
            ->withCount(['absences' => fn($query) => $query->where('round_id', $round->id)])
            ->getMember($memberId);
    }
}
