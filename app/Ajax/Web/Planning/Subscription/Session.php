<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;

use function intval;
use function trans;
use function Jaxon\jq;

/**
 * @databag subscription
 * @before getPool
 */
class Session extends CallableClass
{
    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @exclude
     */
    public function show(PoolModel $pool)
    {
        $this->pool = $pool;
        return $this->home();
    }

    public function home()
    {
        $html = $this->renderView('pages.planning.subscription.session.home', [
            'pool' => $this->pool,
            'total' => $this->poolService->getEnabledSessionCount($this->pool),
        ]);
        $this->response->html('pool-subscription-sessions', $html);
        $this->jq('#btn-subscription-sessions-refresh')->click($this->rq()->home());
        if($this->pool->remit_planned)
        {
            $this->jq('#btn-subscription-beneficiaries')
                ->click($this->rq(Home::class)->beneficiaries());
            $this->jq('#btn-subscription-planning')
                ->click($this->rq(Home::class)->planning());
        }

        $this->bag('subscription')->set('session.filter', false);

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->poolService->getPoolSessionCount($this->pool);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'subscription', 'session.page');
        $sessions = $this->poolService->getPoolSessions($this->pool, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $html = $this->renderView('pages.planning.subscription.session.page', [
            'pool' => $this->pool,
            'sessions' => $sessions,
            'pagination' => $pagination,
        ]);
        $this->response->html('pool-subscription-sessions-page', $html);
        $this->response->call('makeTableResponsive', 'pool-subscription-sessions-page');

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.pool-subscription-session-enable')
            ->click($this->rq()->enableSession($sessionId));
        $this->jq('.pool-subscription-session-disable')
            ->click($this->rq()->disableSession($sessionId)
            ->confirm(trans('tontine.session.questions.disable')));

        return $this->response;
    }

    public function enableSession(int $sessionId)
    {
        $this->poolService->enableSession($this->pool, $sessionId);
        $this->response->html('pool-subscription-sessions-total',
            $this->poolService->getEnabledSessionCount($this->pool));

        return $this->page();
    }

    public function disableSession(int $sessionId)
    {
        $this->poolService->disableSession($this->pool, $sessionId);
        $this->response->html('pool-subscription-sessions-total',
            $this->poolService->getEnabledSessionCount($this->pool));

        return $this->page();
    }
}
