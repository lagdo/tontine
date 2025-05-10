<?php

namespace Ajax\App\Meeting\Summary;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

trait ComponentTrait
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * Get the Jaxon request target
     *
     * @return TargetInterface|null
     */
    abstract protected function target(): ?TargetInterface;

    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get a data bag.
     *
     * @param string  $sBagName
     *
     * @return DataBagContext
     */
    abstract protected function bag(string $sBagName): DataBagContext;

    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        return (int)$this->bag('summary')->get('session.id');
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
        $this->stash()->set('summary.session', $session);
    }
}
