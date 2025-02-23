<?php

namespace Ajax\App\Meeting\Session\Credit\Total;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\Credit\FundTrait;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

/**
 * @databag refund
 */
class Refund extends Component
{
    use FundTrait;

    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.refund.final.home', [
            'session' => $this->stash()->get('meeting.session'),
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund(0);
    }

    /**
     * @param int $fundId
     *
     * @return void
     */
    public function fund(int $fundId)
    {
        $this->bag('refund')->set('fund.id', $fundId);
        $this->bag('refund')->set('principal.page', 1);
        $this->getFund();

        $this->cl(RefundPage::class)->page();
    }
}
