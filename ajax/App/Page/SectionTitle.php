<?php

namespace Ajax\App\Page;

use Jaxon\App\Component;
use Jaxon\Response\AjaxResponse;

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
    public function show(string $title): AjaxResponse
    {
        $this->title = $title;

        return $this->render();
    }
}
