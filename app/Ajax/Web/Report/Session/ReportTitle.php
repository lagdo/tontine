<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\Component;

/**
 * @exclude
 */
class ReportTitle extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('report.session');
        $member = $this->cache->get('report.member');

        return $member === null ? $session->title :
            $session->title . ' - ' . $member->name;
    }
}
