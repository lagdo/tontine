<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

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

    public function html(): string
    {
        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $session = $this->cache->get('summary.session');
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
