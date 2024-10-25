<?php

namespace App\Ajax\Web\Report\Session\Action;

use App\Ajax\Cache;
use App\Ajax\Component;

class Menu extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('report.session');

        return $this->renderView('pages.report.session.action.menu', [
            'sessionId' => $session->id,
        ]);
    }
}
