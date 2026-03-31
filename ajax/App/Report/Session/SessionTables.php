<?php

namespace Ajax\App\Report\Session;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class SessionTables extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.report.session.tables');
    }

    protected function after(): void
    {
        // Initialize the page components.
        $this->cl(Table\BillSession::class)->render();
        $this->cl(Table\BillTotal::class)->render();
        $this->cl(Table\Deposit::class)->render();
        $this->cl(Table\Outflow::class)->render();
        $this->cl(Table\Loan::class)->render();
        $this->cl(Table\Refund::class)->render();
        $this->cl(Table\Remitment::class)->render();
        $this->cl(Table\Saving::class)->render();

        if(!$this->stash()->get('report.member'))
        {
            // Reset the member dropdown to the empty value.
            $this->response()->jq('#report-select-member')->val(0);
        }
    }
}
