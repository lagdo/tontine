<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

use Ajax\App\Meeting\Summary\Component;
use Stringable;

class Fee extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.charge.libre.home', [
            'session' => $this->stash()->get('summary.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(FeePage::class)->page(1);
    }

    /**
     * @exclude
     */
    public function show(): void
    {
        $this->render();
    }
}
