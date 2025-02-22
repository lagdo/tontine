<?php

namespace Ajax\App\Planning\Financial;

use Ajax\FuncComponent;
use Illuminate\Support\Arr;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

/**
 * @databag planning.financial
 * @before getPool
 */
class SessionFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * @var PoolRoundValidator
     */
    protected PoolRoundValidator $validator;

    /**
     * @di $validator
     */
    public function save(array $formValues)
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $values = $this->validator->validateItem($formValues);
        $sessionIds = Arr::only($values, ['start_session_id', 'end_session_id']);
        $this->poolService->saveSessions($pool, $sessionIds);

        // Reload the pool to update the start/end sessions.
        $pool = $this->poolService->getPool($pool->id);
        $this->stash()->set('planning.financial.pool', $pool);

        $this->cl(SessionAction::class)->render();
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.saved'));
    }

    public function delete()
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $this->poolService->deleteRound($pool);

        // Reload the pool to update the start/end sessions.
        $pool = $this->poolService->getPool($pool->id);
        $this->stash()->set('planning.financial.pool', $pool);

        $this->cl(SessionAction::class)->render();
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.deleted'));
    }

    public function enableSession(int $sessionId)
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $this->poolService->enableSession($pool, $sessionId);

        // Reload the pool to update the start/end sessions.
        $pool = $this->poolService->getPool($pool->id);
        $this->stash()->set('planning.financial.pool', $pool);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $pool = $this->stash()->get('planning.financial.pool');
        $this->poolService->disableSession($pool, $sessionId);

        // Reload the pool to update the start/end sessions.
        $pool = $this->poolService->getPool($pool->id);
        $this->stash()->set('planning.financial.pool', $pool);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
    }
}
