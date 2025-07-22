<?php

namespace Ajax\Page;

use Jaxon\App\Component;

/**
 * @exclude
 */
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
