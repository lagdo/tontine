<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Exclude]
class Section extends Component
{
    /**
     * The constructor
     *
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @return string
     */
    protected function overrides(): string
    {
        return SectionContent::class;
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $section = $this->stash()->get('section');
        $session = $this->stash()->get('meeting.session');

        return $this->renderTpl("pages.meeting.session.section.$section", [
            'session' => $session,
            'prevSession' => $this->sessionService->getPrevSession($this->round(), $session),
            'nextSession' => $this->sessionService->getNextSession($this->round(), $session),
        ]);
    }
}
