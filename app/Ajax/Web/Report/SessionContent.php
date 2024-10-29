<?php

namespace App\Ajax\Web\Report;

use App\Ajax\Component;

/**
 * @exclude
 * @databag report
 */
class SessionContent extends Component
{
    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        // Render the page title.
        $this->cl(Session\ReportTitle::class)->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.report.session.session');
    }

    protected function after(): void
    {
        // Initialize the page components.
        $this->cl(Session\Bill\Session::class)->render();
        $this->cl(Session\Bill\Total::class)->render();
        $this->cl(Session\Deposit::class)->render();
        $this->cl(Session\Disbursement::class)->render();
        $this->cl(Session\Loan::class)->render();
        $this->cl(Session\Refund::class)->render();
        $this->cl(Session\Remitment::class)->render();
        $this->cl(Session\Saving::class)->render();
        $this->cl(Session\Saving\Fund::class)->clear();

        // Render the page buttons.
        $this->cl(Session\Action\Export::class)->render();
        $this->cl(Session\Action\Menu::class)->render();
    }
}
