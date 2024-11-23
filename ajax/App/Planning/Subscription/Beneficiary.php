<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Ajax\App\SectionContent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;
use Stringable;

use function trans;

/**
 * @databag subscription
 * @before getPool
 */
class Beneficiary extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * The constructor
     *
     * @param SubscriptionService $subscriptionService
     * @param PoolService $poolService
     * @param SummaryService $summaryService
     */
    public function __construct(private SubscriptionService $subscriptionService,
        private PoolService $poolService, private SummaryService $summaryService)
    {}

    public function pool(int $poolId): AjaxResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->cache->get('subscription.pool');
        $this->view()->shareValues($this->summaryService->getPayables($pool));

        return $this->renderView('pages.planning.subscription.beneficiaries', [
            'pool' => $pool,
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

    public function save(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        $pool = $this->cache->get('subscription.pool');
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
