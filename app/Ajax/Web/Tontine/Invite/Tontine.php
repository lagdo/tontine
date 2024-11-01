<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\TontineService;

/**
 * @databag tontine
 */
class Tontine extends Component
{
    /**
     * @param TontineService $tontineService
     */
    public function __construct(private TontineService $tontineService)
    {}

    public function html(): string
    {
        return !$this->tontineService->hasGuestTontines() ? '' :
            $this->renderView('pages.tontine.invite.home');
    }
}
