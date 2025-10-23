<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
class Fund extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.fund.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(FundPage::class)->page();
    }
}
