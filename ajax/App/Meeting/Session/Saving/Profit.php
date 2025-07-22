<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Profit\Fund;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

/**
 * @databag meeting.saving
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
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('profit.fund');
        // Save the session for the profit components.
        $this->stash()->set('profit.session', $session);

        $this->view()->shareValues([
            'withSave' => $fund->end_sid === $session->id,
            'rqProfit' => $this->rq(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
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
        $session = $this->stash()->get('meeting.session');
        $fund = $this->fundService->getDefaultFund($session->round);
        $profitAmount = $fund->end_sid === $session->id ? $fund->profit_amount : 0;

        $this->bag('meeting.saving')->set('fund.id', $fund->id);
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
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.saving.fund');
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
        $fund = $this->stash()->get('meeting.saving.fund');
        // Save data for the profit components.
        $this->stash()->set('profit.fund', $fund);
        $this->stash()->set('profit.amount', $profitAmount);

        $this->render();
    }
}
