<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Charge\Component;
use Stringable;

use function trim;

class Settlement extends Component
{
    /**
     * @var string
     */
    protected $overrides = Fee::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.fixed.settlement.home', [
            'charge' => $this->stash()->get('meeting.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SettlementPage::class)->page();
        $this->cl(SettlementFunc::class)->showTotal();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('settlement.fixed.filter', null);
        $this->bag('meeting')->set('settlement.fixed.search', '');
        $this->bag('meeting')->set('settlement.fixed.page', 1);

        $this->render();
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.fixed.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.fixed.filter', $onlyUnpaid);
        $this->bag('meeting')->set('settlement.fixed.page', 1);

        $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('settlement.fixed.search', trim($search));
        $this->bag('meeting')->set('settlement.fixed.page', 1);

        $this->cl(SettlementPage::class)->page();
    }
}
