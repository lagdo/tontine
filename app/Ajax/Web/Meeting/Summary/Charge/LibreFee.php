<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

/**
 * @exclude
 */
class LibreFee extends Component
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
        return (string)$this->renderView('pages.meeting.summary.charge.libre.home', [
            'session' => Cache::get('summary.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fees-libre-page');
    }
}
