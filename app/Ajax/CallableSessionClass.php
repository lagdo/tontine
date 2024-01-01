<?php

namespace App\Ajax;

use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag meeting
 * @before getSession
 */
class CallableSessionClass extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
    }

    /**
     * @return void
     */
    protected function showBalanceAmounts()
    {
        $this->response->call('showBalanceAmounts');
    }
}
