<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\Refund\FundTrait;
use Stringable;

/**
 * @databag partial.refund
 */
class Refund extends Component
{
    use FundTrait;

    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.refund.partial.home', [
            'session' => $this->stash()->get('meeting.session'),
            'funds' => $this->fundService->getFundList(),
            'currentFundId' => $this->bag('partial.refund')->get('fund.id', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund($this->bag('partial.refund')->get('fund.id', 0));
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('partial.refund')->set('fund.id', $fundId);
        $this->bag('partial.refund')->set('principal.page', 1);
        $this->getFund();

        $this->cl(RefundPage::class)->page();
    }
}
