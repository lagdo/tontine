<?php

namespace App\Ajax\Web\Report\Session;

class Remitment extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        if(!$this->member)
        {
            return $this->renderView('pages.report.session.session.remitments', [
                'pools' => $this->sessionService->getPayables($this->session),
                'auctions' => $this->sessionService->getAuctions($this->session),
            ]);
        }
        return $this->renderView('pages.report.session.member.remitments', [
            'payables' => $this->memberService->getPayables($this->session, $this->member),
            'auctions' => $this->memberService->getAuctions($this->session, $this->member),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-remitments');
    }
}
