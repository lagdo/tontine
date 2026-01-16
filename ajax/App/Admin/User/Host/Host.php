<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Databag('user')]
#[Export(base: ['render'])]
class Host extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.admin.user.host.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(HostPage::class)->page();
    }
}
