<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Stringable;

use function trim;

class Member extends Component
{
    use AmountTrait;
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
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.session.charge.libre.member.home', [
            'charge' => $charge,
            'paid' => $charge->is_fee,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(MemberPage::class)->page();
        $this->showTotal();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId): void
    {
        $this->bag('meeting')->set('fee.member.filter', null);
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.member.page', 1);

        $this->render();
    }

    public function toggleFilter(): void
    {
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting')->set('fee.member.filter', $filter);
        $this->bag('meeting')->set('fee.member.page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search): void
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));
        $this->bag('meeting')->set('fee.member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
