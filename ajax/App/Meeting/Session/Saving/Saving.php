<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\FundTrait;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Saving extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.saving';

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.home', [
            'session' => $this->stash()->get('meeting.session'),
            'funds' => $this->fundService->getFundList()/*->prepend('', 0)*/,
            'fund' => $this->getStashedFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SavingPage::class)->page();
    }

    public function fund(int $fundId)
    {
        $this->bag($this->bagId)->set('page', 1);
        $this->render();
    }

    /**
     * @exclude
     */
    public function show()
    {
        // We need to explicitely get the default fund here.
        $fundId = $this->tenantService->guild()->default_fund?->id ?? 0;
        $this->bag($this->bagId)->set('fund.id', $fundId);
        $this->getFund(true);

        $this->render();
    }
}
