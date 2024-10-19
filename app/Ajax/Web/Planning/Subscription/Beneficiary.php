<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;
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
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function home(): ComponentResponse
    {
        if(!$this->pool || !$this->pool->remit_planned)
        {
            return $this->response;
        }

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function before()
    {
        $this->view()->shareValues($this->summaryService->getPayables($this->pool));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.beneficiaries', [
            'pool' => $this->pool,
            'pools' => $this->subscriptionService->getPools(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }

    /**
     * @di $summaryService
     * @di $subscriptionService
     */
    public function save(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        if(!$this->pool || !$this->pool->remit_planned || $this->pool->remit_auction)
        {
            return $this->response;
        }

        if(!$this->subscriptionService->saveBeneficiary($this->pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->notify->title(trans('common.titles.error'))->error($message);
        }

        return $this->render();
    }
}
