<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @exclude
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
     * @exclude
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.closing.home', [
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
        $this->response->js('Tontine')->makeTableResponsive('content-summary-closings');
    }
}
