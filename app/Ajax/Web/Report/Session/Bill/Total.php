<?php

namespace App\Ajax\Web\Report\Session\Bill;

use App\Ajax\Web\Report\Session\Component;

use function trans;

class Total extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.bills', [
                'title' => trans('tontine.report.titles.bills.total'),
                'charges' => $this->sessionService->getTotalCharges($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.bills.total', [
            'charges' => $this->sessionService->getTotalCharges($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-total-bills');
    }
}
