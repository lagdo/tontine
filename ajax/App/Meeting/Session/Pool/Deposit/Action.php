<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\Component;
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
        $session = $this->cache->get('meeting.session');
        $pool = $this->cache->get('meeting.pool');

        return $this->renderView('pages.meeting.deposit.pool.action', [
            'session' => $session,
            'depositCount' => $this->cache->get('meeting.pool.deposit.count'),
            'receivableCount' => $this->depositService->getReceivableCount($pool, $session),
        ]);
    }
}