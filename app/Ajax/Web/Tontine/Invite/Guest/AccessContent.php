<?php

namespace App\Ajax\Web\Tontine\Invite\Guest;

use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\GuestService;

/**
 * @exclude
 */
class AccessContent extends Component
{
    /**
     * @param GuestService $guestService
     */
    public function __construct(private GuestService $guestService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $invite = $this->cache->get('invite.invite');
        $tontine = $this->cache->get('invite.tontine');
        $access = $this->guestService->getGuestTontineAccess($invite, $tontine);

        return (string)$this->renderView('pages.invite.guest.access.tontine', [
            'tontine' => $tontine,
            'access' => $access,
        ]);
    }
}
