<?php

namespace Ajax\App\Report\Session;

use Ajax\Component;
use Stringable;

/**
 * @exclude
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
    public function html(): Stringable
    {
        return $this->renderView('pages.report.session.session');
    }

    protected function after(): void
    {
        // Initialize the page components.
        $this->cl(Bill\Session::class)->render();
        $this->cl(Bill\Total::class)->render();
        $this->cl(Deposit::class)->render();
        $this->cl(Outflow::class)->render();
        $this->cl(Loan::class)->render();
        $this->cl(Refund::class)->render();
        $this->cl(Remitment::class)->render();
        $this->cl(Saving::class)->render();

        // Render the page buttons.
        $this->cl(Action\Export::class)->render();
        $this->cl(Action\Menu::class)->render();

        if(!$this->stash()->get('report.member'))
        {
            // Reset the member dropdown to the empty value.
            $this->response->jq('#report-select-member')->val(0);
        }
    }
}
