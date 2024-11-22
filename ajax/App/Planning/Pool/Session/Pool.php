<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\App\SectionContent;
use Ajax\Component;

/**
 * @databag pool
 */
class Pool extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.planning.pool.session.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();
    }
}