<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('summary.saving')]
#[Export(base: ['render'], except: ['show'])]
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

    public function show(): void
    {
        $this->render();
    }
}
