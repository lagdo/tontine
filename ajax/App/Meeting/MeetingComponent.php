<?php

namespace Ajax\App\Meeting;

use Ajax\Component;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before getSession
 */
abstract class MeetingComponent extends Component
{
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
        $session = $this->sessionService->getSession($this->getSessionId());
        if($session === null)
        {
            throw new MessageException(trans('meeting.errors.session.not_found'));
        }
        if($this->target()->method() !== 'reports' && !$session->opened)
        {
            throw new MessageException(trans('meeting.errors.session.not_opened'));
        }
        $this->cache()->set('meeting.session', $session);
    }

    /**
     * @return void
     */
    protected function showBalanceAmounts()
    {
        $this->response->js()->showBalanceAmounts();
    }
}
