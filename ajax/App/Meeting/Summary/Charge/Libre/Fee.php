<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Attribute\Export;

#[Export(base: ['render'])]
class Fee extends Component
{
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.summary.charge.libre.home', [
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

    #[Exclude]
    public function show(): void
    {
        $this->render();
    }
}
