<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class PresenceService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
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
}
