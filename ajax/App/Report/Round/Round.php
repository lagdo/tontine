<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Stringable;

/**
 * @before checkHostAccess ["report", "round"]
 * @before checkOpenedSessions
 * @before getPools
 */
class Round extends Component
{
    use PoolTrait;

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
        return $this->renderView('pages.report.round.home', [
            'round' => $this->tenantService->round(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-home');
    }
}
