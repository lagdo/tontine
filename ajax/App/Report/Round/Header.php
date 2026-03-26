<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\App\ComponentDataTrait;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Header extends Component
{
    use ComponentDataTrait;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.report.round.header.menu', [
            'round' => $this->round(),
            'content' => $this->get('content'),
        ]);
    }
}
