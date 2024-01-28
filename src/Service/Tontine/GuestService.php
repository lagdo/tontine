<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\GuestInvite;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;

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
}
