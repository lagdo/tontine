<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Session\Profit\Fund;
use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

/**
 * @databag summary.saving
 */
class Profit extends Component
{
    use FundTrait;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private LocaleService $localeService,
        protected FundService $fundService, protected SavingService $savingService)
    {}

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $session = $this->stash()->get('summary.session');
        // Save the session for the profit components.
        $this->stash()->set('profit.session', $session);

        $this->view()->shareValues([
            'withSave' => false,
            'rqProfit' => $this->rq(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.session.profit.home', [
            'session' => $session,
            'fund' => $this->stash()->get('profit.fund'),
            'funds' => $this->fundService->getSessionFundList($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(Fund::class)->render();
    }

    /**
     * @exclude
     */
    public function show(): void
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->fundService->getFirstFund($session);
        $profitAmount = $fund->end_sid === $session->id ? $fund->profit_amount : 0;

        $this->bag('summary.saving')->set('fund.id', $fund->id);
        // Save data for the profit components.
        $this->stash()->set('profit.fund', $fund);
        $this->stash()->set('profit.amount', $profitAmount);

        $this->render();
    }

    /**
     * @before getFund
     */
    public function fund(int $fundId): void
    {
        $session = $this->stash()->get('summary.session');
        $fund = $this->stash()->get('summary.saving.fund');
        $profitAmount = $fund->end_sid === $session->id ? $fund->profit_amount : 0;

        // Save data for the profit components.
        $this->stash()->set('profit.fund', $fund);
        $this->stash()->set('profit.amount', $profitAmount);

        $this->render();
    }

    /**
     * @before getFund
     */
    public function amount(int $profitAmount): void
    {
        if($profitAmount < 0)
        {
            return;
        }

        $profitAmount = $this->localeService->convertMoneyToInt($profitAmount);
        $fund = $this->stash()->get('summary.saving.fund');
        // Save data for the profit components.
        $this->stash()->set('profit.fund', $fund);
        $this->stash()->set('profit.amount', $profitAmount);

        $this->render();
    }
}
