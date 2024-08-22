<?php

namespace App\Ajax;

use Siak\Tontine\Exception\MessageException;

/**
 * Checks if the session is opened before calling any method.
 */
class OpenedSessionCallable extends SessionCallable
{
    /**
     * @return void
     */
    protected function getSession()
    {
        parent::getSession();
        if($this->target()->method() !== 'reports' && !$this->session->opened)
        {
            throw new MessageException(trans('meeting.errors.session.not_opened'));
        }
    }
}
