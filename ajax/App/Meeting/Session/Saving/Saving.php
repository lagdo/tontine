<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Databag('meeting.saving')]
class Saving extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.saving.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SavingPage::class)->page();
    }

    #[Exclude]
    public function show(): void
    {
        $this->render();
    }
}
