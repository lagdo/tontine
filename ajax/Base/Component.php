<?php

namespace Ajax\Base;

use Ajax\Page\Header\SectionHeader;
use Jaxon\App\NodeComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Databag;

use function trans;

#[Databag('tenant')]
abstract class Component extends BaseComponent
{
    use ComponentTrait;

    /**
     * @param string $section
     * @param string $entry
     *
     * @return void
     */
    protected function setSectionTitle(string $section, string $entry): void
    {
        $this->cl(SectionHeader::class)->show(trans("tontine.menus.$section"),
            trans("tontine.menus.$entry"));
    }
}
