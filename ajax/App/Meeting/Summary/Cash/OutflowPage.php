<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\PageComponent;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Stringable;

class OutflowPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary', 'outflow.page'];

    /**
     * The constructor
     *
     * @param OutflowService $outflowService
     */
    public function __construct(protected OutflowService $outflowService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $session = $this->stash()->get('summary.session');
        return $this->outflowService->getSessionOutflowCount($session);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.outflow.page', [
            'session' => $session,
            'outflows' => $this->outflowService
                ->getSessionOutflows($session, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-outflows');
    }
}
