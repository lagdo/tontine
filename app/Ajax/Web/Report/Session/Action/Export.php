<?php

namespace App\Ajax\Web\Report\Session\Action;

use App\Ajax\Cache;
use App\Ajax\Component;

class Export extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('report.session');

        return $this->renderView('pages.report.session.action.exports', [
            'sessionId' => $session->id,
        ]);
    }
}
