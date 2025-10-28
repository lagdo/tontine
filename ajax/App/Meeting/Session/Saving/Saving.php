<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('meeting.saving')]
#[Export(base: ['render'])]
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
