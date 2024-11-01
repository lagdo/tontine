<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;

use function intval;
use function trans;

/**
 * @databag subscription
 * @before getPool
 */
class Beneficiary extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SummaryService
     */
    public SummaryService $summaryService;

    /**
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function home(): ComponentResponse
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool || !$pool->remit_planned)
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
        $this->view()->shareValues($this->summaryService->getPayables($pool));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.beneficiaries', [
            'pool' => $this->cache->get('planning.pool'),
            'pools' => $this->subscriptionService->getPools(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function save(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool || !$pool->remit_planned || $pool->remit_auction)
        {
            return $this->response;
        }

        if(!$this->subscriptionService->saveBeneficiary($pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->notify->title(trans('common.titles.error'))
                ->error($message);
        }

        return $this->render();
    }
}
