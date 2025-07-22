<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\FuncComponent;
use Siak\Tontine\Validation\Planning\PoolSessionsValidator;

/**
 * @databag planning.pool
 * @before getPool
 */
class SessionFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * @var PoolSessionsValidator
     */
    protected PoolSessionsValidator $validator;

    /**
     * @di $validator
     */
    public function save(array $formValues): void
    {
        $pool = $this->stash()->get('planning.pool');
        $values = $this->validator->validateItem($formValues);
        $this->poolService->saveSessions($pool, $values);

        // Reload the pool to update the start/end sessions.
        $round = $this->stash()->get('tenant.round');
        $pool = $this->poolService->getPool($round, $pool->id);
        $this->stash()->set('planning.pool', $pool);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
        $this->alert()->success(trans('tontine.session.messages.pool.saved'));
    }

    public function enableSession(int $sessionId): void
    {
        $guild = $this->stash()->get('tenant.guild');
        if(!($session = $this->poolService->getGuildSession($guild, $sessionId)))
        {
            return;
        }

        $pool = $this->stash()->get('planning.pool');
        $this->poolService->enableSession($pool, $session);

        // Reload the pool to update the start/end sessions.
        $round = $this->stash()->get('tenant.round');
        $pool = $this->poolService->getPool($round, $pool->id);
        $this->stash()->set('planning.pool', $pool);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
        $this->alert()->success(trans('tontine.session.messages.pool.saved'));
    }

    public function disableSession(int $sessionId): void
    {
        $guild = $this->stash()->get('tenant.guild');
        if(!($session = $this->poolService->getGuildSession($guild, $sessionId)))
        {
            return;
        }

        $pool = $this->stash()->get('planning.pool');
        $this->poolService->disableSession($pool, $session);

        // Reload the pool to update the start/end sessions.
        $round = $this->stash()->get('tenant.round');
        $pool = $this->poolService->getPool($round, $pool->id);
        $this->stash()->set('planning.pool', $pool);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
        $this->alert()->success(trans('tontine.session.messages.pool.saved'));
    }
}
