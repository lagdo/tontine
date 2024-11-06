<?php

namespace App\Ajax\Web\Planning\Pool\Round;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

use function trans;

/**
 * @databag pool.round
 * @before getPool
 */
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @var PoolRoundValidator
     */
    protected PoolRoundValidator $validator;

    /**
     * The constructor
     *
     * @param SessionService $sessionService
     * @param PoolService $poolService
     */
    public function __construct(private SessionService $sessionService,
        private PoolService $poolService)
    {}

    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'home')
        {
            $this->bag('pool.round')->set('pool.id', $this->target()->args()[0]);
        }
        $poolId = (int)$this->bag('pool.round')->get('pool.id');
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

    public function home(int $poolId): AjaxResponse
    {
        if(!$this->cache->get('planning.pool'))
        {
            return $this->response;
        }

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.pool.round.home', [
            'pool' => $this->cache->get('planning.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->setSmScreenHandler('pool-round-sessions-sm-screens-btn',
            'pool-round-sessions-sm-screens');

        $this->cl(Action::class)->render();
        $this->cl(StartSession::class)->showSessionPage();
        $this->cl(EndSession::class)->showSessionPage();
    }

    /**
     * @di $validator
     */
    public function saveRound(array $formValues)
    {
        $pool = $this->cache->get('planning.pool');
        $values = $this->validator->validateItem($formValues);
        $this->poolService->saveRound($pool, $values);

        // Reload the pool
        $this->getPool();
        $this->render();
        $this->notify->info(trans('tontine.pool_round.messages.saved'));

        return $this->response;
    }

    public function deleteRound()
    {
        $pool = $this->cache->get('planning.pool');
        $this->poolService->deleteRound($pool);

        // Reload the pool
        $this->getPool();
        $this->render();
        $this->notify->info(trans('tontine.pool_round.messages.deleted'));

        return $this->response;
    }
}
