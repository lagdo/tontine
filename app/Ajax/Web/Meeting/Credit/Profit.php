<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
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
     * @param ProfitService $profitService
     * @param FundService $fundService
     */
    public function __construct(protected TenantService $tenantService,
        protected LocaleService $localeService, protected ProfitService $profitService,
        protected FundService $fundService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->tenantService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    private function home()
    {
        $profitAmount = $this->profitService->getProfitAmount($this->session, 0);
        $html = $this->view()->render('tontine.pages.meeting.profit.home', [
            'session' => $this->session,
            'profit' => $profitAmount,
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-profits', $html);

        $inputAmount = pm()->input('profit_amount_edit')->toInt();
        $fundId = pm()->select('profit_fund_id')->toInt();
        $this->jq('#btn-profits-refresh')->click($this->rq()->page($inputAmount, $fundId));
        $this->jq('#btn-profits-save')->click($this->rq()->save($inputAmount, $fundId));

        return $this->page($profitAmount, 0);
    }

    public function page(int $profitAmount, int $fundId)
    {
        $savings = $this->profitService->getDistributions($this->session, $profitAmount, $fundId);
        $partUnitValue = $this->profitService->getPartUnitValue($savings);
        $distributionSum = $savings->sum('distribution');
        $html = $this->view()->render('tontine.pages.meeting.profit.details', [
            'profitAmount' => $profitAmount,
            'partUnitValue' => $partUnitValue,
            'distributionSum' => $distributionSum,
            'distributionCount' => $savings
                ->filter(fn($saving) => $saving->distribution > 0)->count(),
            'amounts' => $this->profitService->getSavingAmounts($this->session, $fundId)
        ]);
        $this->response->html('profit_distribution_details', $html);

        $html = $this->view()->render('tontine.pages.meeting.profit.page', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $distributionSum,
        ]);
        $this->response->html('meeting-profits-page', $html);

        return $this->response;
    }

    public function save(int $profitAmount, int $fundId)
    {
        $this->profitService->saveProfitAmount($this->session, $profitAmount, $fundId);
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->page($profitAmount, $fundId);
    }
}
