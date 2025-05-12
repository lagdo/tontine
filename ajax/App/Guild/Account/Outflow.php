<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Stringable;

/**
 * @databag guild.account
 * @before checkHostAccess ["finance", "accounts"]
 */
class Outflow extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.outflow.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(OutflowPage::class)->page();
    }
}
