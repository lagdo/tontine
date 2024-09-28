<?php

namespace App\Ajax\Web\Tontine\Guest;

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
            $this->renderView('pages.tontine.guest.home', [
                'rqTontine' => $this->rq(),
                'rqTontinePage' => $this->rq(TontinePage::class),
                'rqTontinePagination' => $this->rq(TontinePagination::class),
            ]);
    }
}
