<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;
use function trans;

/**
 * @databag report
 * @databag profit
 * @before getSession
 */
class Profit extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param TenantService $tenantService
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param ProfitService $profitService
     */
    public function __construct(protected TenantService $tenantService,
        protected LocaleService $localeService, protected FundService $fundService,
        protected ProfitService $profitService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('report')->get('session.id');
        $this->session = $this->tenantService->getSession($sessionId);
    }

    public function home(int $fundId)
    {
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $profitAmount = $this->profitService->getProfitAmount($this->session, $fundId);
        $html = $this->view()->render('tontine.pages.report.session.profit.home', [
            'profit' => $profitAmount,
            'fund' => $funds[$fundId],
        ]);
        $this->response->html('report-profits', $html);

        $inputAmount = pm()->input('profit_amount')->toInt();
        $this->jq('#btn-profits-refresh')->click($this->rq()->page($fundId, $inputAmount));

        return $this->page($fundId, $profitAmount);
    }

    public function page(int $fundId, int $profitAmount)
    {
        $savings = $this->profitService->getDistributions($this->session, $fundId, $profitAmount);
        $partUnitValue = $this->profitService->getPartUnitValue($savings);
        $distributionSum = $savings->sum('distribution');
        $html = $this->view()->render('tontine.pages.report.session.profit.details', [
            'profitAmount' => $profitAmount,
            'partUnitValue' => $partUnitValue,
            'distributionSum' => $distributionSum,
            'distributionCount' => $savings
                ->filter(fn($saving) => $saving->distribution > 0)->count(),
            'amounts' => $this->profitService->getSavingAmounts($this->session, $fundId)
        ]);
        $this->response->html('profit_distribution_details', $html);

        $html = $this->view()->render('tontine.pages.report.session.profit.page', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $distributionSum,
        ]);
        $this->response->html('meeting-profits-page', $html);

        return $this->response;
    }
}
