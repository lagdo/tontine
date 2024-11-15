<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;

/**
 * @exclude
 */
class Disbursement extends Component
{
    /**
     * @var DisbursementValidator
     */
    protected DisbursementValidator $validator;

    /**
     * The constructor
     *
     * @param DisbursementService $disbursementService
     */
    public function __construct(protected DisbursementService $disbursementService)
    {}

    public function html(): string
    {
        $session = $this->cache->get('summary.session');

        return $this->renderView('pages.meeting.summary.disbursement.home', [
            'session' => $session,
            'disbursements' => $this->disbursementService->getSessionDisbursements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-disbursements');
        $this->response->js()->showBalanceAmountsWithDelay();
    }
}
