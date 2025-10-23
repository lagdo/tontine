<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class ReportHeader extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');
        $title = !$member ? $session->title : "{$session->title} - {$member->name}";

        return $this->renderView('pages.report.session.header', [
            'reportTitle' => $title,
            'session' => $session,
            'member' => $member,
        ]);
    }
}
