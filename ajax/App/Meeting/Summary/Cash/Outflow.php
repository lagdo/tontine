<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Stringable;

/**
 * @exclude
 */
class Outflow extends Component
{
    /**
     * The constructor
     *
     * @param OutflowService $outflowService
     */
    public function __construct(protected OutflowService $outflowService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.outflow.home', [
            'session' => $session,
            'outflows' => $this->outflowService->getSessionOutflows($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-summary-outflows');
    }
}
