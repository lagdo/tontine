<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\Cache;
use App\Ajax\Component;
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
        $session = Cache::get('summary.session');
        $bills = $this->feeService->getBills($session);
        $settlements = $this->feeService->getSettlements($session);

        return (string)$this->renderView('pages.meeting.summary.charge.fixed.page', [
            'session' => $session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
        ]);
    }
}
