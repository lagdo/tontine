<?php

namespace App\Ajax\Web\Report;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\SummaryService;

/**
 * @databag meeting
 */
class Round extends CallableClass
{
    /**
     * @param SummaryService $summaryService
     */
    public function __construct(private SummaryService $summaryService)
    {}

    /**
     * @before checkGuestAccess ["report", "round"]
     * @before checkOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $round = $this->tenantService->round();
        $html = $this->renderView('pages.report.round.home', [
            'round' => $round,
            'figures' => $this->summaryService->getFigures($round),
            'clPool' => $this->cl(Round\Pool::class),
        ]);
        $this->response->html('content-home', $html);

        $this->response->js()->makeTableResponsive('content-pools');
        $this->response->js()->makeTableResponsive('content-amounts');

        return $this->response;
    }
}
