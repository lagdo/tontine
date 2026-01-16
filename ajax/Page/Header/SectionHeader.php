<?php

namespace Ajax\Page\Header;

use Ajax\Base\Component;
use Jaxon\App\RenderViewTrait;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class SectionHeader extends Component
{
    use RenderViewTrait;

    /**
     * @return void
     */
    protected function setupComponent(): void
    {
        $this->setViewPrefix(self::$tontineViewPrefix);
    }

    /**
     * @param string $section
     * @param string $entry
     *
     * @return void
     */
    public function title(string $section, string $entry): void
    {
        $this->item('title')->renderView('parts.header.section.title', [
            'section' => $section,
            'entry' => $entry,
        ]);
    }

    /**
     * @return void
     */
    public function currency(): void
    {
        $this->item('currency')->renderView('parts.header.section.currency');
    }
}
