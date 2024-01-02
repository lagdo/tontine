<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use App\Ajax\Web\Planning\Pool;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag pool.round
 * @before getPool
 */
class PoolRound extends CallableClass
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
        $startSession = !$this->pool->pool_round ? $defaultLabel : $this->pool->start_date;
        $endSession =  !$this->pool->pool_round ? $defaultLabel : $this->pool->end_date;
        $this->response->html('pool-round-start-session-title',
            trans('tontine.pool_round.titles.start_session', ['session' => $startSession]));
        $this->response->html('pool-round-end-session-title',
            trans('tontine.pool_round.titles.end_session', ['session' => $endSession]));
    }

    private function getSessionPageNumber(SessionModel $session): int
    {
        $sessionCount = $this->sessionService->getTontineSessionCount($session, true, false);
        return (int)($sessionCount / $this->tenantService->getLimit()) + 1;
    }

    public function home(int $poolId)
    {
        if(!$this->pool)
        {
            return $this->response;
        }

        $html = $this->render('pages.planning.pool.round.home', [
            'pool' => $this->pool,
        ]);
        $this->response->html('content-home', $html);
        $this->showTitles();
        $this->jq('#btn-show-start-session-page')->click($this->rq()->showStartSessionPage());
        $this->jq('#btn-show-end-session-page')->click($this->rq()->showEndSessionPage());

        $this->bag('pool.round')->set('pool.id', $poolId);

        $startPageNumber = 1;
        $endPageNumber = 1;
        if($this->pool->pool_round !== null)
        {
            // Go to the pages of the round start and end sessions.
            $startPageNumber = $this->getSessionPageNumber($this->pool->pool_round->start_session);
            $endPageNumber = $this->getSessionPageNumber($this->pool->pool_round->end_session);
        }
        $this->showStartSession($startPageNumber);
        $this->showEndSession($endPageNumber);

        return $this->response;
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
        $this->showTitles();
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

    private function showSession($request, int $sessionId, int $pageNumber, string $field)
    {
        $sessionCount = $this->sessionService->getTontineSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'pool.round', "session.$field.page");
        $sessions = $this->sessionService->getTontineSessions($pageNumber, orderAsc: false);
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
        $sessionId = $this->pool->pool_round ? $this->pool->pool_round->start_session_id : 0;
        $request = $this->rq()->showStartSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'start');
    }

    public function showEndSession(int $pageNumber = 0)
    {
        $sessionId = $this->pool->pool_round ? $this->pool->pool_round->end_session_id : 0;
        $request = $this->rq()->showEndSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'end');
    }

    public function showStartSessionPage()
    {
        if(!$this->pool->pool_round)
        {
            return $this->response;
        }

        $sessionId = $this->pool->pool_round->start_session_id;
        $pageNumber = $this->getSessionPageNumber($this->pool->pool_round->start_session);
        $request = $this->rq()->showStartSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'start');
    }

    public function showEndSessionPage()
    {
        if(!$this->pool->pool_round)
        {
            return $this->response;
        }

        $sessionId = $this->pool->pool_round->end_session_id;
        $pageNumber = $this->getSessionPageNumber($this->pool->pool_round->end_session);
        $request = $this->rq()->showEndSession();
        return $this->showSession($request, $sessionId, $pageNumber, 'end');
    }
}
