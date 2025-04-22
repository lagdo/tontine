<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Stringable;

/**
 * @databag meeting.saving
 */
class Saving extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.home');
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
