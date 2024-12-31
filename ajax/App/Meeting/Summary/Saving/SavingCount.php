<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

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

    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->stash()->get('meeting.saving.fund');

        return $this->renderView('pages.meeting.summary.saving.total', [
            'savingCount' => $this->savingService->getSavingCount($session, $fund),
            'savingTotal' => $this->savingService->getSavingTotal($session, $fund),
        ]);
    }
}
