<?php

namespace App\Ajax\Web\Tontine\Invite\Host;

use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\InviteService;

/**
 * @exclude
 */
class AccessContent extends Component
{
    /**
     * @param InviteService $inviteService
     */
    public function __construct(private InviteService $inviteService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $invite = $this->cache->get('invite.invite');
        $tontine = $this->cache->get('invite.tontine');

        return (string)$this->renderView('pages.invite.guest.access.tontine', [
            'tontine' => $tontine,
            'access' => $this->inviteService->getGuestTontineAccess($invite, $tontine),
        ]);
    }
}
