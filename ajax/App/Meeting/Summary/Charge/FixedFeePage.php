<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Stringable;

/**
 * @exclude
 */
class FixedFeePage extends Component
{
    /**
     * The constructor
     *
     * @param FixedFeeService $feeService
     */
    public function __construct(protected FixedFeeService $feeService)
    {}

    public function html(): Stringable
    {
        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $session = $this->stash()->get('summary.session');
        $bills = $this->feeService->getBills($session);
        $settlements = $this->feeService->getSettlements($session);

        return $this->renderView('pages.meeting.summary.charge.fixed.page', [
            'session' => $session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
        ]);
    }
}
