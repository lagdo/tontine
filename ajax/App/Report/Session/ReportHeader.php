<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
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
