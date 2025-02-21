<?php

namespace Ajax\App\Meeting\Session;

use Ajax\App\Meeting\Component;
use Ajax\App\Page\SectionContent;
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
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $section = $this->stash()->get('section');
        return $this->renderView("pages.meeting.session.section.$section", [
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }
}
