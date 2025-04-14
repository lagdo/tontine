<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Stringable;

/**
 * @databag tontine
 */
class Disbursement extends Component
{
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.disbursement.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(DisbursementPage::class)->page();
    }
}
