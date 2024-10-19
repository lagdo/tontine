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
abstract class OpenedSessionComponent extends Component
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
     * @return int
     */
    protected function getSessionId(): int
    {
        return (int)$this->bag('meeting')->get('session.id');
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $this->session = $this->sessionService->getSession($this->getSessionId());
        if($this->session === null)
        {
            throw new MessageException(trans('meeting.errors.session.not_found'));
        }
        if($this->target()->method() !== 'reports' && !$this->session->opened)
        {
            throw new MessageException(trans('meeting.errors.session.not_opened'));
        }
    }

    /**
     * @return void
     */
    protected function showBalanceAmounts()
    {
        $this->response->js()->showBalanceAmounts();
    }
}
