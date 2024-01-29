<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\GuestInvite;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;

use function count;
use function json_encode;
use function json_decode;
use function now;

class GuestService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of invites sent.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getHostInvites(int $page = 0): Collection
    {
        return $this->tenantService->user()->host_invites()
            ->page($page, $this->tenantService->getLimit())
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @return int
     */
    public function getHostInviteCount(): int
    {
        return $this->tenantService->user()->host_invites()->count();
    }

    /**
     * Get a paginated list of invites received.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getGuestInvites(int $page = 0): Collection
    {
        return $this->tenantService->user()->guest_invites()
            ->page($page, $this->tenantService->getLimit())
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get the number of invites.
     *
     * @return int
     */
    public function getGuestInviteCount(): int
    {
        return $this->tenantService->user()->guest_invites()->count();
    }

    /**
     * Find an invite.
     *
     * @param int $inviteId       The invite id
     *
     * @return GuestInvite|null
     */
    public function getHostInvite(int $inviteId): ?GuestInvite
    {
        return $this->tenantService->user()->host_invites()->find($inviteId);
    }

    /**
     * Find an invite.
     *
     * @param int $inviteId       The invite id
     *
     * @return GuestInvite|null
     */
    public function getGuestInvite(int $inviteId): ?GuestInvite
    {
        return $this->tenantService->user()->guest_invites()->find($inviteId);
    }

    /**
     * Create an invite.
     *
     * @param string $guestEmail
     *
     * @return void
     */
    public function createInvite(string $guestEmail)
    {
        // The current user is the host.
        $host = $this->tenantService->user();
        $guest = User::where('email', $guestEmail)
            ->with('guest_invites', fn($query) => $query->where('host_id', $host->id))
            ->first();
        if(!$guest)
        {
            throw new MessageException(trans('tontine.invite.errors.user_not_found'));
        }
        if($guest->id === $host->id || $guest->guest_invites->count() > 0)
        {
            throw new MessageException(trans('tontine.invite.errors.cannot_invite'));
        }

        $invite = new GuestInvite();
        $invite->status = GuestInvite::STATUS_PENDING;
        $invite->active = true;
        // One week validity by default.
        $invite->expires_at = now()->addWeek();
        $invite->host()->associate($host);
        $invite->guest()->associate($guest);
        $invite->save();
    }

    /**
     * Accept an invite.
     *
     * @param int $inviteId
     *
     * @return void
     */
    public function acceptInvite(int $inviteId)
    {
        if(!($invite = $this->getGuestInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        if($invite->is_expired)
        {
            throw new MessageException(trans('tontine.invite.errors.invite_expired'));
        }
        if(!$invite->is_pending)
        {
            throw new MessageException(trans('tontine.invite.errors.not_allowed'));
        }
        $invite->update(['status' => GuestInvite::STATUS_ACCEPTED]);
    }

    /**
     * Refuse an invite.
     *
     * @param int $inviteId
     *
     * @return void
     */
    public function refuseInvite(int $inviteId)
    {
        if(!($invite = $this->getGuestInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        if($invite->is_expired)
        {
            throw new MessageException(trans('tontine.invite.errors.invite_expired'));
        }
        if(!$invite->is_pending)
        {
            throw new MessageException(trans('tontine.invite.errors.not_allowed'));
        }
        $invite->update(['status' => GuestInvite::STATUS_REFUSED]);
    }

    /**
     * Cancel an invite.
     *
     * @param int $inviteId
     *
     * @return void
     */
    public function cancelInvite(int $inviteId)
    {
        if(!($invite = $this->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        if($invite->is_expired)
        {
            throw new MessageException(trans('tontine.invite.errors.invite_expired'));
        }
        if(!$invite->is_pending)
        {
            throw new MessageException(trans('tontine.invite.errors.not_allowed'));
        }
        $invite->update(['status' => GuestInvite::STATUS_CANCELLED]);
    }

    /**
     * Delete an invite.
     *
     * @param GuestInvite $invite
     *
     * @return void
     */
    private function deleteInvite(GuestInvite $invite)
    {
        DB::transaction(function() use($invite) {
            DB::table('guest_tontine')->where('invite_id', $invite->id)->delete();
            $invite->delete();
        });
    }

    /**
     * Delete an invite.
     *
     * @param int $inviteId
     *
     * @return void
     */
    public function deleteHostInvite(int $inviteId)
    {
        if(!($invite = $this->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }

        $this->deleteInvite($invite);
    }

    /**
     * Delete an invite.
     *
     * @param int $inviteId
     *
     * @return void
     */
    public function deleteGuestInvite(int $inviteId)
    {
        if(!($invite = $this->getGuestInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }

        $this->deleteInvite($invite);
    }

    /**
     * Get the guest access on a given tontine
     *
     * @param GuestInvite $invite
     * @param Tontine $tontine
     *
     * @return array
     */
    public function getGuestTontineAccess(GuestInvite $invite, Tontine $tontine): array
    {
        $inviteTontine = $invite->tontines()->find($tontine->id);
        return !$inviteTontine ? [] : json_decode($inviteTontine->pivot->access, true);
    }

    /**
     * Get the guest access on a given tontine
     *
     * @param GuestInvite $invite
     * @param Tontine $tontine
     * @param array $access
     *
     * @return void
     */
    public function saveGuestTontineAccess(GuestInvite $invite, Tontine $tontine, array $access)
    {
        DB::transaction(function() use($invite, $tontine, $access) {
            $invite->tontines()->detach($tontine->id);
            if(count($access) > 0)
            {
                $invite->tontines()->attach($tontine->id, ['access' => json_encode($access)]);
            }
        });
    }
}
