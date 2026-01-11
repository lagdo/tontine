<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Stringable;

class FeePage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'fee.libre.page'];

    /**
     * The constructor
     *
     * @param LibreFeeService $feeService
     */
    public function __construct(protected LibreFeeService $feeService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->feeService->getFeeCount($this->round());
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.charge.libre.page', [
            'session' => $session,
            'charges' => $this->feeService->getFees($this->round(), $this->currentPage()),
            'bills' => $this->feeService->getBills($session),
            'settlements' => $this->feeService->getSettlements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')
            ->makeTableResponsive('content-session-fees-libre-page');
    }
}
