<?php

namespace Ajax\User\Host;

use Ajax\Component;
use Stringable;

/**
 * @databag user
 */
class Host extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.host.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(HostPage::class)->page();
    }
}
