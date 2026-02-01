<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

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
        return $this->renderTpl('pages.meeting.summary.charge.libre.settlement.home', [
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
        $this->bag('summary')->set('settlement.libre.search', '');
        $this->bag('summary')->set('settlement.libre.filter', null);
        $this->bag('summary')->set('settlement.libre.page', 1);

        $this->render();
    }

    public function toggleFilter(): void
    {
        $onlyUnpaid = $this->bag('summary')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('summary')->set('settlement.libre.filter', $onlyUnpaid);
        $this->bag('summary')->set('settlement.libre.page', 1);

        $this->cl(SettlementPage::class)->page();
    }

    public function search(string $search): void
    {
        $this->bag('summary')->set('settlement.libre.search', trim($search));
        $this->bag('summary')->set('settlement.libre.page', 1);

        $this->cl(SettlementTotal::class)->render();
        $this->cl(SettlementPage::class)->page();
    }
}
