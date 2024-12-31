<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Stringable;

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

    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.charge.libre.home', [
            'session' => $this->stash()->get('summary.session'),
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
