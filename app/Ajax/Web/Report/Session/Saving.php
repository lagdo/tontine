<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;

/**
 * @databag report
 * @before getSession
 * @before getFund
 */
class Saving extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund = null;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param SessionService $sessionService
     * @param ProfitService $profitService
     */
    public function __construct(private LocaleService $localeService,
        private FundService $fundService, private SessionService $sessionService,
        private ProfitService $profitService, private ClosingService $closingService)
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
     * @return void
     */
    protected function getFund()
    {
        $fundId = $this->bag('report')->get('fund.id', 0);
        if($this->target()->method() === 'home')
        {
            $fundId = $this->target()->args()[0];
            $this->bag('report')->set('fund.id', $fundId);
        }
        $this->fund = $this->fundService->getFund($fundId, true, true);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, FundModel $fund)
    {
        $this->bag('report')->set('session.id', $session->id);
        $this->session = $session;
        $this->bag('report')->set('fund.id', $fund->id);
        $this->fund = $fund;

        return $this->home($fund->id, true);
    }

    public function home(int $fundId, bool $backButton = false)
    {
        $profitAmount = $this->closingService->getProfitAmount($this->session, $this->fund);
        $html = $this->renderView('pages.report.session.savings.home', [
            'profit' => $profitAmount,
            'fund' => $this->fund,
            'backButton' => $backButton,
        ]);
        $this->response->html('report-fund-savings', $html);

        $inputAmount = pm()->input('fund-profit-amount')->toInt();
        $this->jq('#btn-fund-savings-refresh')->click($this->rq()->fund($inputAmount));

        return $this->fund($profitAmount);
    }

    public function fund(int $profitAmount)
    {
        $savings = $this->profitService->getDistributions($this->session, $this->fund, $profitAmount);
        $partUnitValue = $this->profitService->getPartUnitValue($savings);
        $distributionSum = $savings->sum('distribution');
        $distributionCount = $savings->filter(fn($saving) => $saving->distribution > 0)->count();
        $html = $this->renderView('pages.report.session.savings.details', [
            'profitAmount' => $profitAmount,
            'partUnitValue' => $partUnitValue,
            'distributionSum' => $distributionSum,
            'distributionCount' => $distributionCount,
            'amounts' => $this->profitService->getSavingAmounts($this->session, $this->fund),
        ]);
        $this->response->html('report-fund-profits-distribution', $html);

        $html = $this->renderView('pages.report.session.savings.page', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $distributionSum,
        ]);
        $this->response->html('report-fund-savings-page', $html);
        $this->response->call('makeTableResponsive', 'report-fund-savings-page');

        return $this->response;
    }
}
