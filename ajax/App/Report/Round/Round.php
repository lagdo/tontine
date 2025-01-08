<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Siak\Tontine\Service\Meeting\SummaryService;
use Stringable;

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
     * @before checkHostAccess ["report", "round"]
     * @before checkOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->tenantService->round();

        return $this->renderView('pages.report.round.home', [
            'round' => $round,
            'figures' => $this->summaryService->getFigures($round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-pools');
        $this->response->js('Tontine')->makeTableResponsive('content-amounts');
    }
}
