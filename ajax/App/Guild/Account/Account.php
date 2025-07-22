<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag guild.account
 * @before checkHostAccess ["finance", "accounts"]
 */
class Account extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     */
    public function home(): void
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.finance'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.account.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->setSmScreenHandler('account-sm-screens');

        $this->cl(Fund::class)->render();
        $this->cl(Outflow::class)->render();
    }
}
