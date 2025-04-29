<?php

namespace Ajax\App\Meeting\Session;

use Ajax\App\Page\SectionContent;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

/**
 * @exclude
 */
class Section extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

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
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $section = $this->stash()->get('section');
        $session = $this->stash()->get('meeting.session');

        return $this->renderView("pages.meeting.session.section.$section", [
            'session' => $session,
            'prevSession' => $this->sessionService->getPrevSession($session),
            'nextSession' => $this->sessionService->getNextSession($session),
        ]);
    }
}
