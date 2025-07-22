<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Pool\PoolTrait;
use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;
use Stringable;

use function trans;

/**
 * @databag planning.pool
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
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SummaryService $summaryService
     */
    public function __construct(PoolService $poolService, private SummaryService $summaryService)
    {
        $this->poolService = $poolService;
    }

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $pool = $this->stash()->get('planning.pool');
        if(!$pool->remit_planned)
        {
            throw new MessageException(trans('tontine.pool.errors.not_planned'));
        }
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('planning.pool');
        $this->view()->shareValues($this->summaryService->getPayables($pool));

        return $this->renderView('pages.planning.pool.subscription.beneficiaries');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')
            ->makeTableResponsive('content-subscription-beneficiaries');
    }

    /**
     * @di $subscriptionService
     *
     * @param int $sessionId
     * @param int $nextSubscriptionId
     * @param int $currSubscriptionId
     *
     * @return void
     */
    public function save(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        $pool = $this->stash()->get('planning.pool');
        if(!$pool || !$pool->remit_planned || $pool->remit_auction)
        {
            return;
        }

        if(!$this->subscriptionService->saveBeneficiary($pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->alert()->title(trans('common.titles.error'))
                ->error($message);
        }

        $this->render();
    }
}
