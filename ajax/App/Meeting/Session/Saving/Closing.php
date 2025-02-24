<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag report
 */
class Closing extends Component
{
    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param ClosingService $closingService
     */
    public function __construct(protected FundService $fundService,
        protected ClosingService $closingService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        return $this->renderView('pages.meeting.closing.home', [
            'session' => $session,
            'funds' => $this->fundService->getFundList(),
            'closings' => $this->closingService->getClosings($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-closings');
        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $session = $this->stash()->get('meeting.session');
        $this->bag('report')->set('session.id', $session->id);
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
