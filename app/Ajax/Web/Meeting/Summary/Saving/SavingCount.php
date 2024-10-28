<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;

/**
 * @exclude
 */
class SavingCount extends Component
{
    /**
     * The constructor
     *
     * @param SavingService $savingService
     */
    public function __construct(protected SavingService $savingService)
    {}

    public function html(): string
    {
        $session = $this->cache->get('summary.session');
        $fund = $this->cache->get('meeting.saving.fund');

        return (string)$this->renderView('pages.meeting.summary.saving.total', [
            'savingCount' => $this->savingService->getSavingCount($session, $fund),
            'savingTotal' => $this->savingService->getSavingTotal($session, $fund),
        ]);
    }
}
