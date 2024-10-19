<?php

namespace App\Ajax\Web\Report;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Meeting\SummaryService;

/**
 * @databag meeting
 */
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param SummaryService $summaryService
     */
    public function __construct(private SummaryService $summaryService)
    {}

    /**
     * @before checkGuestAccess ["report", "round"]
     * @before checkOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->tenantService->round();

        return (string)$this->renderView('pages.report.round.home', [
            'round' => $round,
            'figures' => $this->summaryService->getFigures($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function after()
    {
        $this->response->js()->makeTableResponsive('content-pools');
        $this->response->js()->makeTableResponsive('content-amounts');
    }
}
