<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Stringable;

/**
 * @databag summary.saving
 */
class Saving extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.saving.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SavingPage::class)->page();
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
