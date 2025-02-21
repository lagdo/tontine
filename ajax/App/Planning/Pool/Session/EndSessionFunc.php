<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

/**
 * @databag pool.session
 * @before getPool
 */
class EndSessionFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * @var PoolRoundValidator
     */
    protected PoolRoundValidator $validator;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    /**
     * @di $validator
     */
    public function save(array $formValues)
    {
        $pool = $this->stash()->get('pool.session.pool');
        $values = $this->validator->end()->validateItem($formValues);
        $this->poolService->saveEndSession($pool, $values);

        // Reload the pool
        $this->getPool();
        $this->cl(EndSessionTitle::class)->render();
        $this->cl(EndSessionAction::class)->render();
        $this->cl(EndSessionPage::class)->page();
        $this->cl(PoolPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.saved'));
    }

    public function delete()
    {
        $pool = $this->stash()->get('pool.session.pool');
        $this->poolService->deleteRound($pool);

        // Reload the pool
        $this->getPool();
        $this->cl(EndSessionTitle::class)->render();
        $this->cl(EndSessionAction::class)->render();
        $this->cl(EndSessionPage::class)->page();
        $this->cl(PoolPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.deleted'));
    }
}
