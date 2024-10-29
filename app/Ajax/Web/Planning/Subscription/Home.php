<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;

use function intval;
use function trans;

/**
 * @databag subscription
 * @before setPool
 */
class Home extends Component
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
    protected function setPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @exclude
     *
     * @return PoolModel|null
     */
    public function getPool(): ?PoolModel
    {
        if(!$this->pool)
        {
            $poolId = intval($this->bag('subscription')->get('pool.id'));
            $this->pool = $this->poolService->getPool($poolId);
        }

        return $this->pool;
    }

    public function pool(int $poolId = 0): ComponentResponse
    {
        if($this->pool === null)
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));

        $this->bag('subscription')->set('pool.id', $this->pool->id);
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');
        $this->bag('subscription')->set('session.filter', false);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
        $this->cl(SessionPage::class)->page();

        $this->response->js()->setSmScreenHandler('pool-subscription-sm-screens');
    }
}
