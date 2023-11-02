<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\CallableClass;
use App\Ajax\Web\Planning\Subscription;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\SessionService;

use function intval;
use function Jaxon\jq;

/**
 * @databag subscription
 * @before getPool
 */
class Session extends CallableClass
{
    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     */
    public function __construct(PoolService $poolService, TenantService $tenantService,
        SessionService $sessionService)
    {
        $this->poolService = $poolService;
        $this->tenantService = $tenantService;
        $this->sessionService = $sessionService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @exclude
     */
    public function show(PoolModel $pool)
    {
        $this->pool = $pool;
        return $this->home($pool->id);
    }

    public function home(int $poolId)
    {
        $html = $this->view()->render('tontine.pages.planning.subscription.session.home')
            ->with('pool', $this->pool);
        $this->response->html('pool-subscription-sessions', $html);
        $this->jq('#btn-subscription-sessions-refresh')->click($this->rq()->home($poolId));
        if($this->pool->remit_planned)
        {
            $this->jq('#btn-subscription-planning')
                ->click($this->cl(Subscription::class)->rq()->planning($poolId));
        }

        $this->bag('subscription')->set('pool.id', $poolId);
        $this->bag('subscription')->set('session.filter', false);

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount, 'subscription', 'session.page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $html = $this->view()->render('tontine.pages.planning.subscription.session.page')
            ->with('pool', $this->pool)
            ->with('sessions', $sessions)
            ->with('total', $this->tenantService->countEnabledSessions($this->pool))
            ->with('pagination', $pagination);
        $this->response->html('pool-subscription-sessions-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.pool-subscription-session-toggle')->click($this->rq()->toggleSession($sessionId));

        return $this->response;
    }

    public function toggleSession(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $this->sessionService->toggleSession($this->pool, $session);

        return $this->page();
    }
}
