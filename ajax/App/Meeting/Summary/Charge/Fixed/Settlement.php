<?php

namespace Ajax\App\Meeting\Summary\Charge\Fixed;

use Ajax\App\Meeting\Summary\Charge\Component;

class Settlement extends Component
{
    use ChargeTrait;

    /**
     * @var string
     */
    protected string $overrides = Fee::class;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.summary.charge.fixed.settlement.home', [
            'charge' => $this->stash()->get('summary.session.charge'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SettlementTotal::class)->render();
        $this->cl(SettlementPage::class)->page();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId): void
    {
        $this->bag('summary')->set('settlement.fixed.filter', null);
        $this->bag('summary')->set('settlement.fixed.search', '');
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->render();
    }

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('summary')->get('settlement.fixed.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('settlement.fixed.filter', $onlyUnpaid);
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search): void
    {
        $this->bag('summary')->set('settlement.fixed.search', trim($search));
        $this->bag('summary')->set('settlement.fixed.page', 1);

        $this->cl(SettlementTotal::class)->render();
        $this->cl(SettlementPage::class)->page();
    }
}
