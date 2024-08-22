<?php

namespace App\Ajax;

use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class SessionCallable extends CallableClass
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
        if($this->session === null)
        {
            throw new MessageException(trans('meeting.errors.session.not_found'));
        }
    }

    /**
     * @return void
     */
    protected function showBalanceAmounts()
    {
        $this->response->call('showBalanceAmounts');
    }
}
