<?php

namespace App\Ajax\Web\Report\Session;

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
        return $this->member === null ? $this->session->title :
            $this->session->title . ' - ' . $this->member->name;
    }
}
