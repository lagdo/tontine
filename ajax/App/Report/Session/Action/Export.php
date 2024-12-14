<?php

namespace Ajax\App\Report\Session\Action;

use Ajax\Component;
use Stringable;

class Export extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache()->get('report.session');

        return $this->renderView('pages.report.session.action.exports', [
            'sessionId' => $session->id,
        ]);
    }
}
