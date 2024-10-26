<?php

namespace App\Ajax\Web\Meeting\Session\Saving;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;

/**
 * @exclude
 */
class MemberTotal extends Component
{
    /**
     * The constructor
     *
     * @param SavingService $savingService
     */
    public function __construct(private SavingService $savingService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');

        return (string)$this->renderView('pages.meeting.saving.total', [
            'savingCount' => $this->savingService->getSavingCount($session, $fund),
            'savingTotal' => $this->savingService->getSavingTotal($session, $fund),
        ]);
    }
}
