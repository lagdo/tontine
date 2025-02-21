<?php

namespace Ajax\App\Meeting\Session\Cash;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Stringable;

class Disbursement extends Component
{
    /**
     * The constructor
     *
     * @param DisbursementService $disbursementService
     */
    public function __construct(protected DisbursementService $disbursementService)
    {}

    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $disbursements = $this->disbursementService->getSessionDisbursements($session);

        return $this->renderView('pages.meeting.disbursement.home', [
            'session' => $session,
            'disbursements' => $disbursements,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Balance::class)->render();
        $this->response->js('Tontine')
            ->makeTableResponsive('content-session-disbursements');
    }
}
