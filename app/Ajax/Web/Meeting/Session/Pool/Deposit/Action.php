<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Deposit;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Pool\DepositService;

/**
 * @exclude
 */
class Action extends Component
{
    /**
     * The constructor
     *
     * @param DepositService $depositService
     */
    public function __construct(protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('meeting.session');
        $pool = Cache::get('meeting.pool');

        return (string)$this->renderView('pages.meeting.deposit.pool.action', [
            'session' => $session,
            'depositCount' => Cache::get('meeting.pool.deposit.count'),
            'receivableCount' => $this->depositService->getReceivableCount($pool, $session),
        ]);
    }
}
