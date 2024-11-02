<?php

namespace App\Ajax\Web\Report\Session;

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
        $this->cl(ReportTitle::class)->render();
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
        $this->cl(Bill\Session::class)->render();
        $this->cl(Bill\Total::class)->render();
        $this->cl(Deposit::class)->render();
        $this->cl(Disbursement::class)->render();
        $this->cl(Loan::class)->render();
        $this->cl(Refund::class)->render();
        $this->cl(Remitment::class)->render();
        $this->cl(Saving::class)->render();
        $this->cl(Saving\Fund::class)->clear();

        // Render the page buttons.
        $this->cl(Action\Export::class)->render();
        $this->cl(Action\Menu::class)->render();
    }
}
