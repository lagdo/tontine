<?php

namespace Ajax\Page\Header;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class SectionHeader extends Component
{
    /**
     * @var string
     */
    private string $section = '';

    /**
     * @var string
     */
    private string $entry = '';

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('parts.header.section', [
            'section' => $this->section,
            'entry' => $this->entry,
        ]);
    }

    /**
     * @param string $section
     * @param string $entry
     *
     * @return void
     */
    public function show(string $section, string $entry): void
    {
        $this->section = $section;
        $this->entry = $entry;
        $this->render();
    }
}
