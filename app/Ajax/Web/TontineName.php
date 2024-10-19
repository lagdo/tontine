<?php

namespace App\Ajax\Web;

use Jaxon\App\Component;
use Jaxon\Response\ComponentResponse;

/**
 * @exclude
 */
class TontineName extends Component
{
    /**
     * @var string
     */
    private string $name = '';

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function show(string $name): ComponentResponse
    {
        $this->name = $name;

        return $this->render();
    }
}
