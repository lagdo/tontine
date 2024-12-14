<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

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
    public function html(): Stringable
    {
        $session = $this->cache()->get('meeting.session');
        $fund = $this->cache()->get('meeting.saving.fund');

        return $this->renderView('pages.meeting.saving.total', [
            'savingCount' => $this->savingService->getSavingCount($session, $fund),
            'savingTotal' => $this->savingService->getSavingTotal($session, $fund),
        ]);
    }
}
