<?php

namespace App\Ajax\Web;

use Jaxon\App\Component;
use Jaxon\Response\ComponentResponse;

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
    public function show(string $title): ComponentResponse
    {
        $this->title = $title;

        return $this->render();
    }
}
