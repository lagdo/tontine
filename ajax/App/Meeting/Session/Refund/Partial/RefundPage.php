<?php

namespace Ajax\App\Meeting\Session\Refund\Partial;

use Ajax\App\Meeting\PageComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Stringable;

/**
 * @databag meeting.refund.partial
 * @before getFund
 */
class RefundPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.refund.partial';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.refund.partial', 'page'];

    /**
     * The constructor
     *
     * @param PartialRefundService $refundService
     */
    public function __construct(protected PartialRefundService $refundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->refundService->getPartialRefundCount($session, $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->renderView('pages.meeting.refund.partial.page', [
            'session' => $session,
            'refunds' => $this->refundService
                ->getPartialRefunds($session, $fund, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')
            ->makeTableResponsive('content-session-partial-refunds-page');
    }
}
