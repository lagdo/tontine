<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Report\Session\Saving;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;

class Closing extends CallableSessionClass
{
    /**
     * The constructor
     *
     * @param ClosingService $closingService
     * @param FundService $fundService
     */
    public function __construct(protected ClosingService $closingService,
        protected FundService $fundService)
    {}

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        $html = $this->renderView('pages.meeting.summary.closing.home', [
            'session' => $this->session,
            'closings' => $this->closingService->getClosings($this->session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-closings', $html);
        $this->response->call('makeTableResponsive', 'meeting-closings');

        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $this->session->id);

        $selectFundId = pm()->select('closings-fund-id')->toInt();
        $this->jq('#btn-fund-show-savings')->click($this->rq()->showSavings($selectFundId));

        return $this->response;
    }

    /**
     * @databag report
     */
    public function showSavings(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->cl(Saving::class)->show($this->session, $fund);

        $this->response->call('showSmScreen', 'report-fund-savings', 'session-savings');

        return $this->response;
    }
}
