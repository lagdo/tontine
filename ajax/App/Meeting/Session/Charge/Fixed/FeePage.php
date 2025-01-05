<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Stringable;

class FeePage extends MeetingPageComponent
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
        return $this->feeService->getFeeCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.charge.fixed.page', [
            'session' => $session,
            'charges' => $this->feeService->getFees($this->currentPage()),
            'bills' => $this->feeService->getBills($session),
            'settlements' => $this->feeService->getSettlements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('meeting-fees-fixed-page');
    }
}
