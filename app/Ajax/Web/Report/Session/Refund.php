<?php

namespace App\Ajax\Web\Report\Session;

class Refund extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.refunds', [
                'refund' => $this->sessionService->getRefund($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.refunds', [
            'refunds' => $this->memberService->getRefunds($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-refunds');
    }
}
