<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Planning\PoolService;

use function intval;
use function trans;

/**
 * @databag subscription
 * @before getPool
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
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

    public function pool(int $poolId = 0): ComponentResponse
    {
        if($this->cache->get('planning.pool') === null)
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

        $pool = $this->cache->get('planning.pool');
        $this->bag('subscription')->set('pool.id', $pool->id);
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
