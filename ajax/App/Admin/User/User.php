<?php

namespace Ajax\App\Admin\User;

use Ajax\App\Admin\User\Guest\Guest;
use Ajax\App\Admin\User\Host\Host;
use Ajax\App\SectionContent;
use Ajax\App\SectionTitle;
use Ajax\Component;
use Jaxon\Response\AjaxResponse;
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
    public function home(): AjaxResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontines'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.user.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Host::class)->render();
        $this->cl(Guest::class)->render();

        $this->response->js()->setSmScreenHandler('invites-sm-screens');
    }
}