<?php

namespace Ajax\Page\Header;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class SectionHeader extends Component
{
    /**
     * @var string
     */
    private string $template;

    /**
     * @var array
     */
    private array $vars;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView($this->template, $this->vars);
    }

    /**
     * @param string $section
     * @param string $entry
     *
     * @return void
     */
    public function title(string $section, string $entry): void
    {
        $this->template = 'parts.header.section.title';
        $this->vars = [
            'section' => $section,
            'entry' => $entry,
        ];
        $this->item('title')->render();
    }

    /**
     * @return void
     */
    public function currency(): void
    {
        $this->template = 'parts.header.section.currency';
        $this->vars = [];
        $this->item('currency')->render();
    }
}
