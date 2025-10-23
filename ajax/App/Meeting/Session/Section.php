<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

#[Exclude]
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
        $round = $this->stash()->get('tenant.round');
        $section = $this->stash()->get('section');
        $session = $this->stash()->get('meeting.session');

        return $this->renderView("pages.meeting.session.section.$section", [
            'session' => $session,
            'prevSession' => $this->sessionService->getPrevSession($round, $session),
            'nextSession' => $this->sessionService->getNextSession($round, $session),
        ]);
    }
}
