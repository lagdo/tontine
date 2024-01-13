<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;

/**
 * @databag report
 * @before getSession
 */
class Saving extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param ProfitService $profitService
     */
    public function __construct(protected LocaleService $localeService,
        protected FundService $fundService, protected ProfitService $profitService,
        protected SessionService $sessionService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('report')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, int $fundId)
    {
        $this->bag('report')->set('session.id', $session->id);
        $this->session = $session;

        return $this->home($fundId);
    }

    public function home(int $fundId)
    {
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $profitAmount = $this->profitService->getProfitAmount($this->session, $fundId);
        $html = $this->render('pages.report.session.savings.home', [
            'profit' => $profitAmount,
            'fund' => $funds[$fundId],
        ]);
        $this->response->html('report-fund-savings', $html);

        $inputAmount = pm()->input('fund-profit-amount')->toInt();
        $this->jq('#btn-fund-savings-refresh')->click($this->rq()->fund($fundId, $inputAmount));

        return $this->fund($fundId, $profitAmount);
    }

    public function fund(int $fundId, int $profitAmount)
    {
        $savings = $this->profitService->getDistributions($this->session, $fundId, $profitAmount);
        $partUnitValue = $this->profitService->getPartUnitValue($savings);
        $distributionSum = $savings->sum('distribution');
        $distributionCount = $savings->filter(fn($saving) => $saving->distribution > 0)->count();
        $html = $this->render('pages.report.session.savings.details', [
            'profitAmount' => $profitAmount,
            'partUnitValue' => $partUnitValue,
            'distributionSum' => $distributionSum,
            'distributionCount' => $distributionCount,
            'amounts' => $this->profitService->getSavingAmounts($this->session, $fundId),
        ]);
        $this->response->html('report-fund-profits-distribution', $html);

        $html = $this->render('pages.report.session.savings.page', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $distributionSum,
        ]);
        $this->response->html('report-fund-savings-page', $html);

        return $this->response;
    }
}
