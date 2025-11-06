<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Jaxon\Attributes\Attribute\Before;
use Stringable;

#[Before('checkChargeEdit')]
class Settlement extends Component
{
    use ChargeTrait;

    /**
     * @var string
     */
    protected $overrides = Fee::class;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.libre.settlement.home', [
            'charge' => $this->stash()->get('meeting.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId): void
    {
        $this->bag('meeting')->set('settlement.libre.search', '');
        $this->bag('meeting')->set('settlement.libre.filter', null);
        $this->bag('meeting')->set('settlement.libre.page', 1);

        $this->render();
    }

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.libre.filter', $onlyUnpaid);
        $this->bag('meeting')->set('settlement.libre.page', 1);

        $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search): void
    {
        $this->bag('meeting')->set('settlement.libre.search', trim($search));
        $this->bag('meeting')->set('settlement.libre.page', 1);

        $this->showSettlementTotal();
        $this->cl(SettlementPage::class)->page();
    }
}
