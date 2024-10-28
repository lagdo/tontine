<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

/**
 * @exclude
 */
class FixedFee extends Component
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
        return (string)$this->renderView('pages.meeting.summary.charge.fixed.home', [
            'session' => $this->cache->get('summary.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fees-fixed-page');
    }
}
