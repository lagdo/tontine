<?php

namespace App\Ajax\Web\Report\Session;

class Disbursement extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.disbursements', [
                'disbursement' => $this->sessionService->getDisbursement($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.disbursements', [
            'disbursements' => $this->memberService->getDisbursements($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-disbursements');
    }
}
