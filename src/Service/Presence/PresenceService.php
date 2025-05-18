<?php

namespace Siak\Tontine\Service\Presence;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
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
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionAbsences(Session $session): Collection
    {
        return $session->absences()->get()->pluck('name', 'id');
    }

    /**
     * @param Round $round
     * @param Member $member
     *
     * @return Collection
     */
    public function getMemberAbsences(Round $round, Member $member): Collection
    {
        return $member->absences()
            ->where('round_id', $round->id)
            ->pluck('title', 'id');
    }

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return void
     */
    public function togglePresence(Session $session, Member $member)
    {
        !$session->absences()->find($member->id) ?
            $session->absences()->attach($member->id) :
            $session->absences()->detach($member->id);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getSessionCount(Round $round): int
    {
        return $this->sessionService->active()->getSessionCount($round);
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param Round $round
     * @param int $page
     *
     * @return Collection
     */
    public function getSessions(Round $round, int $page = 0): Collection
    {
        return $this->sessionService->active()
            ->withCount(['absences'])
            ->getSessions($round, $page, true);
    }

    /**
     * Find a session.
     *
     * @param Round $round
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getSession(Round $round, int $sessionId): ?Session
    {
        return $this->sessionService->active()
            ->withCount(['absences'])
            ->getSession($round, $sessionId);
    }

    /**
     * Get the number of members.
     *
     * @param Round $round
     * @param string $search
     *
     * @return int
     */
    public function getMemberCount(Round $round, string $search = ''): int
    {
        return $this->memberService->getMemberCount($round, $search);
    }

    /**
     * Get a paginated list of members.
     *
     * @param Round $round
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Round $round, string $search, int $page = 0): Collection
    {
        return $this->memberService
            ->withCount(['absences' => fn($query) =>
                $query->where('round_id', $round->id)])
            ->getMembers($round, $search, $page);
    }

    /**
     * Find a member.
     *
     * @param Round $round
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(Round $round, int $memberId): ?Member
    {
        return $this->memberService
            ->withCount(['absences' => fn($query) =>
                $query->where('round_id', $round->id)])
            ->getMember($round, $memberId);
    }
}
