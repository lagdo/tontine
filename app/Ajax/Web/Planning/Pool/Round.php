<?php

namespace App\Ajax\Web\Planning\Pool;

use App\Ajax\CallableClass;
use App\Ajax\Web\Planning\Pool;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolRoundService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag pool.round
 * @before getPool
 */
class Round extends CallableClass
{
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
     * @param PoolRoundService $poolRoundService
     */
    public function __construct(private PoolRoundService $poolRoundService)
    {}

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ? $this->target()->args()[0] :
            (int)$this->bag('pool.round')->get('pool.id');
        $this->pool = $this->poolRoundService->getPool($poolId);
    }

    private function showTitles()
    {
        $html = $this->render('pages.planning.pool.round.actions', [
            'pool' => $this->pool,
        ]);
        $this->response->html('pool-round-actions', $html);
        $this->jq('#btn-pool-round-back')->click($this->cl(Pool::class)->rq()->home());
        $this->jq('#btn-pool-round-save')->click($this->rq()->saveRound(pm()->form('round-form')));
        if($this->pool->pool_round)
        {
            $this->jq('#btn-pool-round-delete')->click($this->rq()->deleteRound()
                ->confirm(trans('tontine.pool_round.questions.delete')));
        }

        $defaultLabel = trans('tontine.pool_round.labels.default');
        $startSession = $this->pool->pool_round ?
            $this->pool->pool_round->start_session->date : $defaultLabel;
        $endSession =  $this->pool->pool_round ?
            $this->pool->pool_round->end_session->date : $defaultLabel;
        $this->response->html('pool-round-start-session-title',
            trans('tontine.pool_round.titles.start_session', ['session' => $startSession]));
        $this->response->html('pool-round-end-session-title',
            trans('tontine.pool_round.titles.end_session', ['session' => $endSession]));
    }

    public function home(int $poolId)
    {
        $html = $this->render('pages.planning.pool.round.home', [
            'pool' => $this->pool,
        ]);
        $this->response->html('content-home', $html);
        $this->showTitles();

        $this->bag('pool.round')->set('pool.id', $poolId);

        $this->showStartSession();
        $this->showEndSession();

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveRound(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->poolRoundService->saveRound($this->pool, $values);

        // Reload the pool
        $this->getPool();
        $this->showTitles();
        $this->notify->info(trans('tontine.pool_round.messages.saved'));

        return $this->response;
    }

    public function deleteRound()
    {
        $this->poolRoundService->deleteRound($this->pool);

        // Reload the pool
        $this->getPool();
        $this->home($this->pool->id);
        $this->notify->info(trans('tontine.pool_round.messages.deleted'));

        return $this->response;
    }

    private function showSession($request, int $sessionId, int $pageNumber, string $field)
    {
        $sessionCount = $this->poolRoundService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'pool.round', "session.$field.page");
        $sessions = $this->poolRoundService->getSessions($pageNumber);
        $pagination = $request->paginate($pageNumber, $perPage, $sessionCount);

        $html = $this->render('pages.planning.pool.round.sessions', [
            'field' => $field,
            'sessions' => $sessions,
            'sessionId' => $sessionId,
            'pagination' => $pagination,
        ]);
        $this->response->html("pool-round-sessions-$field", $html);

        return $this->response;
    }

    public function showStartSession(int $pageNumber = 0)
    {
        $sessionId =  $this->pool->pool_round ? $this->pool->pool_round->start_session_id : 0;
        $request = $this->rq()->showStartSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'start');
    }

    public function showEndSession(int $pageNumber = 0)
    {
        $sessionId =  $this->pool->pool_round ? $this->pool->pool_round->end_session_id : 0;
        $request = $this->rq()->showEndSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'end');
    }
}
