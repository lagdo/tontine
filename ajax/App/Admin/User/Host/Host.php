<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\Component;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

#[Databag('user')]
#[Export(base: ['render'])]
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
