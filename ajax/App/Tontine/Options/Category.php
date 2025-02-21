<?php

namespace Ajax\App\Tontine\Options;

use Ajax\Component;
use Stringable;

/**
 * @databag tontine
 */
class Category extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.options.category.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(CategoryPage::class)->page();
    }
}
