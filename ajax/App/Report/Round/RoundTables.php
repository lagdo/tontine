<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Exclude]
class RoundTables extends Component
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $lastSession = $this->stash()->get('report.session');
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => !$session->pending &&
                $session->day_date <= $lastSession->day_date);
        return $this->renderTpl('pages.report.round.tables', [
            'round' => $this->round(),
            'sessions' => $sessions->pluck('title', 'id'),
        ]);
    }

    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-home');
    }
}
