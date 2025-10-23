<?php

namespace Ajax\Page;

use Jaxon\App\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class SectionContent extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return '';
    }
}
