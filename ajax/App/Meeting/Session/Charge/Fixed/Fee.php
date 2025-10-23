<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

class Fee extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.fixed.home', [
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(FeePage::class)->page(1);
    }

    #[Exclude]
    public function show(): void
    {
        $this->render();
    }
}
