<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag planning
 * @databag subscription
 */
class Subscription extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["planning", "pools"]
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.subscription.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Pool::class)->render();

        $pools = $this->stash()->get('subscription.pools');
        if($pools !== null && $pools->count() > 0)
        {
            $pool = $pools[0];
            $this->bag('subscription')->set('pool.id', $pool->id);
            // Show the start session page of the first pool in the list.
            $this->stash()->set('subscription.pool', $pool);
            $this->cl(Member::class)->pool($pool->id);
        }

        $this->response->js('Tontine')
            ->showSmScreen('content-subscription-pools', 'subscription-sm-screens');
    }
}
