<?php

namespace Ajax\App\Guild\Account;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

use function trans;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
class Account extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Callback('tontine.hideMenu')]
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
        $this->response->jo('tontine')->setSmScreenHandler('account-sm-screens');

        $this->cl(Fund::class)->render();
        $this->cl(Outflow::class)->render();
    }
}
