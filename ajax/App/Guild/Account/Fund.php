<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Stringable;

/**
 * @databag tontine
 * @before checkHostAccess ["finance", "accounts"]
 */
class Fund extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.fund.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FundPage::class)->page();
    }
}
