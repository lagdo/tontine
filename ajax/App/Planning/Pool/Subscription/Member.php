<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Component;
use Ajax\App\Planning\Pool\Pool;
use Ajax\App\Planning\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

#[Before('getPool')]
#[Databag('planning.pool')]
#[Export(base: ['render'])]
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
        $this->bag('planning.pool')->set('member.filter', null);
        $this->bag('planning.pool')->set('member.search', '');
        $this->bag('planning.pool')->set('member.page', 1);

        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.subscription.member.home', [
            'pool' => $pool = $this->stash()->get('planning.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(MemberPage::class)->page();

        $this->response->jo('Tontine')
            ->showSmScreen('content-subscription-members', 'subscription-sm-screens');
    }

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.pool')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.pool')->set('member.filter', $filter);
        $this->bag('planning.pool')->set('member.page', 1);

        // Show the first page
        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('planning.pool')->set('member.search', trim($search));
        $this->bag('planning.pool')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
