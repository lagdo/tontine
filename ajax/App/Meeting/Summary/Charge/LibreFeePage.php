<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

/**
 * @exclude
 */
class LibreFeePage extends Component
{
    /**
     * The constructor
     *
     * @param LibreFeeService $feeService
     */
    public function __construct(protected LibreFeeService $feeService)
    {}

    public function html(): string
    {
        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $session = $this->cache->get('summary.session');
        $bills = $this->feeService->getBills($session);
        $settlements = $this->feeService->getSettlements($session);

        return $this->renderView('pages.meeting.summary.charge.libre.page', [
            'session' => $session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
        ]);
    }
}
