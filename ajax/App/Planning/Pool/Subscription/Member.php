<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\Component;
use Ajax\App\Planning\Pool\Pool;
use Ajax\App\Planning\Pool\PoolTrait;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

/**
 * @databag planning.finance.pool
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
    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    public function pool(int $poolId)
    {
        $this->bag('planning.finance.pool')->set('member.filter', null);
        $this->bag('planning.finance.pool')->set('member.search', '');
        $this->bag('planning.finance.pool')->set('member.page', 1);

        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.subscription.member.home', [
            'pool' => $pool = $this->stash()->get('planning.finance.pool'),
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

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.finance.pool')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.finance.pool')->set('member.filter', $filter);
        $this->bag('planning.finance.pool')->set('member.page', 1);

        // Show the first page
        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('planning.finance.pool')->set('member.search', trim($search));
        $this->bag('planning.finance.pool')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
