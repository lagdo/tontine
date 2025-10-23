<?php

namespace Ajax\Page;

use Jaxon\App\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class SectionTitle extends Component
{
    /**
     * @var string
     */
    private string $title = '';

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function show(string $title): void
    {
        $this->title = $title;

        $this->render();
    }
}
