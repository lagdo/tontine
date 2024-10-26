<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\FundService;

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
    protected function before()
    {
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $fundId > 0 ? $this->fundService->getFund($fundId, true, true) : null;
        Cache::set('meeting.saving.fund', $fund);
    }

    /**
     * @exclude
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.summary.saving.home', [
            'session' => Cache::get('summary.session'),
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', 0),
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SavingPage::class)->page();
        $this->cl(SavingCount::class)->render();

        $this->response->js()->makeTableResponsive('meeting-savings-page');
        $this->response->js()->showSmScreen('report-fund-savings', 'session-savings');
    }

    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);

        return $this->render();
    }
}
