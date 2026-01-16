<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class ReportHeader extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');
        $title = !$member ? $session->title : "{$session->title} - {$member->name}";

        return $this->renderTpl('pages.report.session.header', [
            'reportTitle' => $title,
            'session' => $session,
            'member' => $member,
        ]);
    }
}
