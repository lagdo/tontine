<?php

namespace App\Ajax\Web\Report\Round;

use App\Ajax\Component;
use App\Ajax\Web\Component\SectionContent;
use Jaxon\Response\AjaxResponse;
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
    public function home(): AjaxResponse
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
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-pools');
        $this->response->js()->makeTableResponsive('content-amounts');
    }
}
