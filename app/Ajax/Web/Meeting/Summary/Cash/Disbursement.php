<?php

namespace App\Ajax\Web\Meeting\Summary\Cash;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;

/**
 * @exclude
 */
class Disbursement extends SessionCallable
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

    public function show(SessionModel $session)
    {
        $disbursements = $this->disbursementService->getSessionDisbursements($session);

        $html = $this->renderView('pages.meeting.summary.disbursement.home', [
            'session' => $session,
            'disbursements' => $disbursements,
        ]);
        $this->response->html('meeting-disbursements', $html);
        $this->response->call('makeTableResponsive', 'meeting-disbursements');

        $this->response->call('showBalanceAmountsWithDelay');

        return $this->response;
    }
}
