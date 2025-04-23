<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\App\Planning\Finance\Pool\Pool;
use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

/**
 * @databag subscription
 * @before getPool
 */
class Member extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Pool::class;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService,)
    {}

    public function pool(int $poolId)
    {
        $this->bag('subscription')->set('member.filter', null);
        $this->bag('subscription')->set('member.search', '');

        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.subscription.member.home', [
            'pool' => $this->stash()->get('subscription.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();

        $this->response->js('Tontine')
            ->showSmScreen('content-subscription-members', 'subscription-sm-screens');
    }
}
