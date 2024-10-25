<?php

namespace App\Ajax\Web\Report\Session\Bill;

use App\Ajax\Cache;
use App\Ajax\Component;
use Siak\Tontine\Service\Report\SessionService;

use function trans;

class Total extends Component
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = Cache::get('report.session');
        $member = Cache::get('report.member');

        if(!$member)
        {
            return $this->renderView('pages.report.session.session.bills', [
                'title' => trans('tontine.report.titles.bills.total'),
                'charges' => $this->sessionService->getTotalCharges($session),
            ]);
        }
        return $this->renderView('pages.report.session.member.bills.total', [
            'charges' => $this->sessionService->getTotalCharges($session, $member),
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
