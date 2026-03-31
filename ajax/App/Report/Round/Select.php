<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\App\ComponentDataTrait;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Select extends Component
{
    use ComponentDataTrait;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $sessions = $this->stash()->get('report.sessions');
        return $this->renderTpl('pages.report.round.select', [
            'sessions' => $sessions->pluck('title', 'id'),
            'content' => $this->get('content'),
        ]);
    }
}
