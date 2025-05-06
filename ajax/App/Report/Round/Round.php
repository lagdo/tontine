<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Service\Meeting\SummaryService;
use Stringable;

/**
 * @databag report
 * @before checkHostAccess ["report", "round"]
 * @before checkOpenedSessions
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
     * @callback jaxon.ajax.callback.hideMenuOnMobile
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
        $figures = $this->summaryService->getFigures($round); 
        $pools = $this->summaryService->getPoolsBalance($figures);
        $this->view()->share('figures', $figures);
        $this->view()->share('pools', $pools);

        return $this->renderView('pages.report.round.home', [
            'round' => $round,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-home');
    }
}
