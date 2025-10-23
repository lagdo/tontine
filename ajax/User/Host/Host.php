<?php

namespace Ajax\User\Host;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Databag;
use Stringable;

#[Databag('user')]
class Host extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.user.host.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(HostPage::class)->page();
    }
}
