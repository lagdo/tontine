<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
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
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

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
        $this->pool = $this->poolService->getPool($poolId);
    }

    private function getSessionPageNumber(SessionModel $session): int
    {
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    public function home(int $poolId): ComponentResponse
    {
        if(!$this->pool)
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
        $this->bag('pool.round')->set('pool.id', $this->pool->id);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.pool.round.home', [
            'pool' => $this->pool,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->setSmScreenHandler('pool-round-sessions-sm-screens-btn',
            'pool-round-sessions-sm-screens');

        $startSession = $endSession = trans('tontine.pool_round.labels.default');
        $startPageNumber = $endPageNumber = 1;
        if($this->pool->pool_round !== null)
        {
            $startSession = $this->pool->start_date;
            $endSession = $this->pool->end_date;
            // Go to the pages of the round start and end sessions.
            $startPageNumber = $this->getSessionPageNumber($this->pool->pool_round->start_session);
            $endPageNumber = $this->getSessionPageNumber($this->pool->pool_round->end_session);
        }

        $this->cl(PoolRoundAction::class)->pool($this->pool);
        $this->cl(PoolRoundStartSession::class)->pool($this->pool, $startPageNumber);
        $this->cl(PoolRoundEndSession::class)->pool($this->pool, $endPageNumber);

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
        $values = $this->validator->validateItem($formValues);
        $this->poolService->saveRound($this->pool, $values);

        // Reload the pool
        $this->getPool();
        $this->cl(PoolRoundAction::class)->render();
        $this->notify->info(trans('tontine.pool_round.messages.saved'));

        return $this->response;
    }

    public function deleteRound()
    {
        $this->poolService->deleteRound($this->pool);

        // Reload the pool
        $this->getPool();
        $this->home($this->pool->id);
        $this->notify->info(trans('tontine.pool_round.messages.deleted'));

        return $this->response;
    }
}
