<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Databag('summary.saving')]
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
