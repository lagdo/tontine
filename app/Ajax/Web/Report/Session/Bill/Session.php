<?php

namespace App\Ajax\Web\Report\Session\Bill;

use App\Ajax\Web\Report\Session\Component;

use function trans;

class Session extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.bills', [
                'title' => trans('tontine.report.titles.bills.session'),
                'charges' => $this->sessionService->getSessionCharges($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.bills.session', [
            'bills' => $this->memberService->getBills($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-session-bills');
    }
}
