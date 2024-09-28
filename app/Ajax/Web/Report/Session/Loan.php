<?php

namespace App\Ajax\Web\Report\Session;

class Loan extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.loans', [
                'loan' => $this->sessionService->getLoan($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.loans', [
            'loans' => $this->memberService->getLoans($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-loans');
    }
}
