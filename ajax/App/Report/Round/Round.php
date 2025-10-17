<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Service\Meeting\Session\SessionService;
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
     * @param SessionService $sessionService
     * @param SummaryService $summaryService
     */
    public function __construct(private SessionService $sessionService,
        private SummaryService $summaryService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $this->view()->share('lastSession', $this->stash()->get('tenant.session'));

        $round = $this->tenantService->round();
        $sessions = $this->sessionService
            ->getSessions($round, orderAsc: false)
            ->filter(fn($session) => $session->active);
        return $this->renderView('pages.report.round.home', [
            'round' => $round,
            'sessions' => $sessions->pluck('title', 'id'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-home');
    }

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     *
     * @return void
     */
    public function home(): void
    {
        $this->render();
    }

    /**
     * @param int $sessionId
     *
     * @return void
     */
    public function select(int $sessionId): void
    {
        $this->render();
    }
}
