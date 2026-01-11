<?php

namespace Ajax\App\Guild\Account;

use Ajax\Base\Guild\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
#[Export(base: ['render'])]
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
