<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\App\ComponentDataTrait;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Exclude]
class Select extends Component
{
    use ComponentDataTrait;

    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));
        return $this->renderTpl('pages.report.round.select', [
            'sessions' => $sessions->pluck('title', 'id'),
            'content' => $this->get('content'),
        ]);
    }
}
