<?php

namespace App\Ajax\Web\Report\Session\Action;

use App\Ajax\Component;

class Menu extends Component
{
    /**
     * @var int
     */
    private int $sessionId;

    /**
     * @exclude
     *
     * @param int $sessionId
     *
     * @return self
     */
    public function setSessionId(int $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.report.session.action.menu', [
            'sessionId' => $this->sessionId,
        ]);
    }
}
