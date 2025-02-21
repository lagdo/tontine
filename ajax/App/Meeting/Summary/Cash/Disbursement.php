<?php

namespace Ajax\App\Meeting\Summary\Cash;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Stringable;

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

    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');

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
        $this->response->js('Tontine')->makeTableResponsive('content-summary-disbursements');
    }
}
