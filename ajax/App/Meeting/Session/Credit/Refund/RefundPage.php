<?php

namespace Ajax\App\Meeting\Session\Credit\Refund;

use Ajax\App\Meeting\Session\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Stringable;

#[Before('getFund')]
#[Databag('meeting.refund')]
class RefundPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.refund', 'debt.page'];

    /**
     * The constructor
     *
     * @param RefundService $refundService
     */
    public function __construct(protected RefundService $refundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('debt.filter', null);

        return $this->refundService->getDebtCount($session, $fund, $filtered);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $filtered = $this->bag($this->bagId)->get('debt.filter', null);
        $debts = $this->refundService
            ->getDebts($session, $fund, $filtered, $this->currentPage());

        return $this->renderView('pages.meeting.session.refund.page', [
            'debts' => $debts,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-refunds-page');
    }
}
