<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Session\PageComponent;
use Siak\Tontine\Service\Meeting\Cash\OutflowService;
use Stringable;

class OutflowPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'outflow.page'];

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
        $session = $this->stash()->get('meeting.session');
        return $this->outflowService->getSessionOutflowCount($session);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.session.outflow.page', [
            'session' => $session,
            'outflows' => $this->outflowService
                ->getSessionOutflows($session, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-session-outflows');
    }
}
