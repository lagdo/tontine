<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Tontine\FundService;

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
    public function html(): string
    {
        $session = $this->cache->get('summary.session');

        return (string)$this->renderView('pages.meeting.summary.closing.home', [
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
        $this->response->js()->makeTableResponsive('meeting-closings');
    }
}
