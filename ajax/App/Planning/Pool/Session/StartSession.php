<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;
use Stringable;

/**
 * @databag pool.session
 * @before getPool
 */
class StartSession extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = PoolSection::class;

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

    public function pool(int $poolId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.start.home', [
            'pool' => $this->stash()->get('pool.session.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(StartSessionTitle::class)->render();
        $this->cl(StartSessionAction::class)->render();
        $this->cl(StartSessionPage::class)->current();

        $this->response->js('Tontine')->showSmScreen('content-planning-sessions', 'pool-sm-screens');
    }

    /**
     * @di $validator
     */
    public function save(array $formValues)
    {
        $pool = $this->stash()->get('pool.session.pool');
        $values = $this->validator->start()->validateItem($formValues);
        $this->poolService->saveStartSession($pool, $values);

        // Reload the pool
        $this->getPool();
        $this->cl(StartSessionTitle::class)->render();
        $this->cl(StartSessionAction::class)->render();
        $this->cl(StartSessionPage::class)->page();
        $this->cl(PoolPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.saved'));
    }

    public function delete()
    {
        $pool = $this->stash()->get('pool.session.pool');
        $this->poolService->deleteRound($pool);

        // Reload the pool
        $this->getPool();
        $this->cl(StartSessionTitle::class)->render();
        $this->cl(StartSessionAction::class)->render();
        $this->cl(StartSessionPage::class)->page();
        $this->cl(PoolPage::class)->page();

        $this->alert()->info(trans('tontine.pool_round.messages.deleted'));
    }
}
