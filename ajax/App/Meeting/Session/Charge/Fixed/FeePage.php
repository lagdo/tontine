<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\PageComponent;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Stringable;

class FeePage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.fixed.page'];

    /**
     * The constructor
     *
     * @param FixedFeeService $feeService
     */
    public function __construct(protected FixedFeeService $feeService)
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
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.session.charge.fixed.page', [
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
        $this->response->jo('tontine')->makeTableResponsive('content-session-fees-fixed-page');
    }
}
