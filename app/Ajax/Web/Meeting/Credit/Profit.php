<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function Jaxon\pm;

/**
 * @databag meeting
 * @databag profit
 * @before getSession
 */
class Profit extends CallableClass
{
    /**
     * @var TenantService
     */
    public TenantService $tenantService;

    /**
     * @var LocaleService
     */
    public LocaleService $localeService;

    /**
     * @var ProfitService
     */
    protected ProfitService $profitService;

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
     */
    public function __construct(TenantService $tenantService,
        LocaleService $localeService, ProfitService $profitService)
    {
        $this->tenantService = $tenantService;
        $this->localeService = $localeService;
        $this->profitService = $profitService;
    }

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
        $totalProfit = $this->profitService->getTotalProfit($this->session);
        $html = $this->view()->render('tontine.pages.meeting.profit.home', [
            'session' => $this->session,
            'totalProfit' => $totalProfit,
            'profitAmount' => $totalProfit,
        ]);
        $this->response->html('meeting-profits', $html);

        $inputAmount = pm()->input('profit_amount_edit')->toInt();
        $this->jq('#btn-profits-refresh')->click($this->rq()->page($inputAmount));
        $this->jq('#btn-profits-save')->click($this->rq()->save($inputAmount));

        return $this->page($totalProfit);
    }

    public function page(int $profitAmount)
    {
        $this->response->html('profit_amount_show',
            $this->localeService->formatMoney($profitAmount, true));
        $html = $this->view()->render('tontine.pages.meeting.profit.page', [
            'fundings' => $this->profitService->getDistributions($this->session),
            'profitAmount' => $profitAmount,
        ]);
        $this->response->html('meeting-profits-page', $html);

        return $this->response;
    }

    public function save(int $profitAmount)
    {
        $this->profitService->saveProfit($this->session, $profitAmount);
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->page($profitAmount);
    }
}
