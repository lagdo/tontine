<?php

namespace App\Ajax\Web\Planning\Pool;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

use function Jaxon\jaxon;
use function trans;

/**
 * @databag pool.round
 * @before getPool
 */
class PoolRound extends Component
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
        $poolId = $this->target()->method() === 'home' ? $this->target()->args()[0] :
            (int)$this->bag('pool.round')->get('pool.id');
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

    public function home(int $poolId): ComponentResponse
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
    protected function before()
    {
        $pool = $this->cache->get('planning.pool');
        $this->bag('pool.round')->set('pool.id', $pool->id);
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

        $pool = $this->cache->get('planning.pool');
        $startSession = $endSession = trans('tontine.pool_round.labels.default');
        if($pool->pool_round !== null)
        {
            $startSession = $pool->start_date;
            $endSession = $pool->end_date;
        }

        $this->cl(PoolRoundAction::class)->render();
        $this->cl(PoolRoundStartSession::class)->showSessionPage();
        $this->cl(PoolRoundEndSession::class)->showSessionPage();

        $response = jaxon()->getResponse();
        $response->html('pool-round-start-session-title',
            trans('tontine.pool_round.titles.start_session', ['session' => $startSession]));
        $response->html('pool-round-end-session-title',
            trans('tontine.pool_round.titles.end_session', ['session' => $endSession]));
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
        $this->cl(PoolRoundAction::class)->render();
        $this->notify->info(trans('tontine.pool_round.messages.saved'));

        return $this->response;
    }

    public function deleteRound()
    {
        $pool = $this->cache->get('planning.pool');
        $this->poolService->deleteRound($pool);

        // Reload the pool
        $this->getPool();
        $this->home($pool->id);
        $this->notify->info(trans('tontine.pool_round.messages.deleted'));

        return $this->response;
    }
}
