<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\App\ComponentDataTrait;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class Header extends Component
{
    use ComponentDataTrait;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

        return $this->renderTpl('pages.report.session.header.menu', [
            'title' => !$member ? $session->title : "{$session->title} - {$member->name}",
            'session' => $session,
            'member' => $member,
            'content' => $this->get('content'),
        ]);
    }
}
