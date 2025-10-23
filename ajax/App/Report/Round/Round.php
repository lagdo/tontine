<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Stringable;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
#[Before('getPools')]
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
     * @return void
     */
    #[Callback('jaxon.ajax.callback.hideMenuOnMobile')]
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
