<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\GuestInvite;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;

use function count;
use function now;

class UserService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of invites sent.
     *
     * @param User $user
     * @param int $page
     *
     * @return Collection
     */
    public function getHostInvites(User $user, int $page = 0): Collection
    {
        return $user->host_invites()
            ->page($page, $this->tenantService->getLimit())
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param User $user
     *
     * @return int
     */
    public function getHostInviteCount(User $user): int
    {
        return $user->host_invites()->count();
    }

    /**
     * Get a paginated list of invites received.
     *
     * @param User $user
     * @param int $page
     *
     * @return Collection
     */
    public function getGuestInvites(User $user, int $page = 0): Collection
    {
        return $user->guest_invites()
            ->page($page, $this->tenantService->getLimit())
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get the number of invites.
     *
     * @param User $user
     *
     * @return int
     */
    public function getGuestInviteCount(User $user): int
    {
        return $user->guest_invites()->count();
    }

    /**
     * Find an invite.
     *
     * @param User $user
     * @param int $inviteId
     *
     * @return GuestInvite|null
     */
    public function getHostInvite(User $user, int $inviteId): ?GuestInvite
    {
        return $user->host_invites()->find($inviteId);
    }

    /**
     * Find an invite.
     *
     * @param User $user
     * @param int $inviteId
     *
     * @return GuestInvite|null
     */
    public function getGuestInvite(User $user, int $inviteId): ?GuestInvite
    {
        return $user->guest_invites()->find($inviteId);
    }

    /**
     * Create an invite.
     *
     * @param User $host
     * @param string $guestEmail
     *
     * @return void
     */
    public function createInvite(User $host, string $guestEmail)
    {
        // The current user is the host.
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
     * @param User $user
     * @param int $inviteId
     *
     * @return void
     */
    public function acceptInvite(User $user, int $inviteId)
    {
        if(!($invite = $this->getGuestInvite($user, $inviteId)))
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
     * @param User $user
     * @param int $inviteId
     *
     * @return void
     */
    public function refuseInvite(User $user, int $inviteId)
    {
        if(!($invite = $this->getGuestInvite($user, $inviteId)))
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
     * @param User $user
     * @param int $inviteId
     *
     * @return void
     */
    public function cancelInvite(User $user, int $inviteId)
    {
        if(!($invite = $this->getHostInvite($user, $inviteId)))
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
            DB::table('guest_options')->where('invite_id', $invite->id)->delete();
            $invite->delete();
        });
    }

    /**
     * Delete an invite.
     *
     * @param User $user
     * @param int $inviteId
     *
     * @return void
     */
    public function deleteHostInvite(User $user, int $inviteId)
    {
        if(!($invite = $this->getHostInvite($user, $inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }

        $this->deleteInvite($invite);
    }

    /**
     * Delete an invite.
     *
     * @param Guild $guild
     * @param int $inviteId
     *
     * @return bool
     */
    public function deleteGuestInvite(Guild $guild, int $inviteId): bool
    {
        if(!($invite = $this->getGuestInvite($guild->user, $inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }

        $inviteIsDeleted = DB::table('guest_options')
            ->where('invite_id', $invite->id)
            ->where('guild_id', $guild->id)
            ->exists();
        $this->deleteInvite($invite);

        return $inviteIsDeleted;
    }

    /**
     * Get the guest access on a given guild
     *
     * @param GuestInvite $invite
     * @param Guild $guild
     *
     * @return array
     */
    public function getHostGuildAccess(GuestInvite $invite, Guild $guild): array
    {
        $inviteTontine = $invite->guilds()->find($guild->id);
        return !$inviteTontine ? [] : $inviteTontine->options->access;
    }

    /**
     * Get the guest access on a given guild
     *
     * @param GuestInvite $invite
     * @param Guild $guild
     * @param array $access
     *
     * @return void
     */
    public function saveHostGuildAccess(GuestInvite $invite, Guild $guild, array $access)
    {
        DB::transaction(function() use($invite, $guild, $access) {
            $invite->guilds()->detach($guild->id);
            if(count($access) > 0)
            {
                $invite->guilds()->attach($guild->id, ['access' => $access]);
            }
        });
    }
}
