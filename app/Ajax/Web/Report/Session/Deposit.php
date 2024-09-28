<?php

namespace App\Ajax\Web\Report\Session;

class Deposit extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.deposits', [
                'pools' => $this->sessionService->getReceivables($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.deposits', [
            'receivables' => $this->memberService->getReceivables($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-deposits');
    }
}
