<?php

namespace Ajax\App\Meeting\Summary\Charge\Fixed;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Export(base: ['render'])]
class Fee extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.charge.fixed.home', [
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
