<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\Cache;
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
        $session = Cache::get('report.session');
        $member = Cache::get('report.member');

        return $member === null ? $session->title :
            $session->title . ' - ' . $member->name;
    }
}
