<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Before('getFund')]
#[Databag('meeting.refund')]
class Refund extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund';

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $funds = $this->fundService->getSessionFundList($session, false);
        $funds->prepend('', 0);
        return $this->renderView('pages.meeting.session.refund.home', [
            'session' => $session,
            'funds' => $funds,
            'fund' => $this->getStashedFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(RefundPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return void
     */
    public function fund(int $fundId): void
    {
        $this->bag($this->bagId)->set('debt.page', 1);

        $this->render();
    }

    #[Exclude]
    public function show(): void
    {
        // We need to explicitely get the default fund here.
        $this->bag($this->bagId)->set('fund.id', 0);
        $this->bag($this->bagId)->set('debt.page', 1);
        $this->stash()->set("{$this->bagId}.fund", null);

        $this->render();
    }

    public function toggleFilter(): void
    {
        $filtered = $this->bag($this->bagId)->get('debt.filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag($this->bagId)->set('debt.filter', $filtered);
        $this->bag($this->bagId)->set('debt.page', 1);

        $this->cl(RefundPage::class)->page();
    }
}
