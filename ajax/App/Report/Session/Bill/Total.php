<?php

namespace Ajax\App\Report\Session\Bill;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Report\SessionService;
use Stringable;

use function trans;

#[Exclude]
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
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $member = $this->stash()->get('report.member');

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
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-report-total-bills');
    }
}
