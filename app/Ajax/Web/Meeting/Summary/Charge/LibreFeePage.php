<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\Component;
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

        return (string)$this->renderView('pages.meeting.summary.charge.libre.page', [
            'session' => $session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
        ]);
    }
}
