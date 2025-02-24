<?php

namespace Ajax\App\Meeting\Session\Refund\Total;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Stringable;

/**
 * @databag meeting.refund.final
 * @before getFund
 */
class Refund extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.final';

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
            'fund' => $this->getStashedFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(RefundPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return void
     */
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
        $fundId = $this->tenantService->tontine()->default_fund?->id ?? 0;
        $this->bag($this->bagId)->set('fund.id', $fundId);
        $this->getFund(true);

        $this->render();
    }
}
