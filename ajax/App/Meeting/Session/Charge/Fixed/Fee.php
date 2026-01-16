<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Export;

#[Export(base: ['render'], except: ['show'])]
class Fee extends Component
{
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.charge.fixed.home', [
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

    public function show(): void
    {
        $this->render();
    }
}
