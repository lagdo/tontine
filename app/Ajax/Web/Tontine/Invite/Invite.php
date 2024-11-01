<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;

use function trans;

/**
 * @databag invite
 */
class Invite extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
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
    public function html(): string
    {
        return (string)$this->renderView('pages.invite.home');
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
