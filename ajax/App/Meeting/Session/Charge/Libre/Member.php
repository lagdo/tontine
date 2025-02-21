<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Stringable;

class Member extends Component
{
    use AmountTrait;

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

        return $this->renderView('pages.meeting.charge.libre.member.home', [
            'charge' => $charge,
            'paid' => $charge->is_fee,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
        $this->showTotal();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('fee.member.filter', null);
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.member.page', 1);

        $this->render();
    }
}
