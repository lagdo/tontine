<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Stringable;

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
        $session = $this->stash()->get('meeting.session');
        $outflows = $this->outflowService->getSessionOutflows($session);

        return $this->renderView('pages.meeting.outflow.home', [
            'session' => $session,
            'outflows' => $outflows,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Balance::class)->render();
        $this->response->js('Tontine')
            ->makeTableResponsive('content-session-outflows');
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
