<?php

namespace App\Ajax\Web\Component;

use Jaxon\App\Component;
use Jaxon\Response\AjaxResponse;

/**
 * @exclude
 */
class Organisation extends Component
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
    public function show(string $name): AjaxResponse
    {
        $this->name = $name;

        return $this->render();
    }
}
