<?php

namespace Ajax\App\Report\Session\Action;

use Ajax\Component;

class Export extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('report.session');

        return $this->renderView('pages.report.session.action.exports', [
            'sessionId' => $session->id,
        ]);
    }
}
