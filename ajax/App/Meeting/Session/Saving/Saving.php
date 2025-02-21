<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag meeting.saving
 */
class Saving extends Component
{
    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.home', [
            'session' => $this->stash()->get('meeting.session'),
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', 0),
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund((int)$this->bag('meeting.saving')->get('fund.id', 0));
    }

    protected function getFund()
    {
        $fund = null;
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        if($fundId > 0 && ($fund = $this->fundService->getFund($fundId, true, true)) === null)
        {
            $this->bag('meeting.saving')->set('fund.id', 0);
        }
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);
        $this->bag('meeting.saving')->set('page', 1);
        $this->getFund();

        $this->cl(SavingPage::class)->page();
    }
}
