<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Stringable;

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

    public function html(): Stringable
    {
        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $session = $this->stash()->get('summary.session');
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
