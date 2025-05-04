<?php

namespace Ajax\User;

use Ajax\User\Guest\Guest;
use Ajax\User\Host\Host;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Ajax\Component;
use Stringable;

use function trans;

/**
 * @databag user
 */
class User extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.admin'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Host::class)->render();
        $this->cl(Guest::class)->render();

        $this->response->js('Tontine')->setSmScreenHandler('invites-sm-screens');
    }
}
