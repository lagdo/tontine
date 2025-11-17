<?php

namespace Ajax\User;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('user')]
#[Export(base: ['render'])]
class User extends Component
{
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
    protected function after(): void
    {
        $this->cl(Host\Host::class)->render();
        $this->cl(Guest\Guest::class)->render();

        $this->response->jo('tontine')
            ->setSmScreenHandler('invites-sm-screens', 'invites-content');
    }
}
