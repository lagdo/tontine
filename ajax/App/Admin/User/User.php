<?php

namespace Ajax\App\Admin\User;

use Ajax\Base\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["admin", "users"])]
#[Databag('admin')]
class User extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["admin", "users"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.admin.user.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(Host\Host::class)->render();
        $this->cl(Guest\Guest::class)->render();

        $this->response()->jo('tontine')
            ->setSmScreenHandler('invites-sm-screens', 'invites-content');
    }
}
