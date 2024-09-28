<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;
use Illuminate\Support\Collection;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag report
 * @before getSession
 * @before getFund
 */
class Fund extends Component
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
     * @var Collection
     */
    protected Collection $savings;

    /**
     * @var int|null
     */
    protected ?int $profitAmount = null;

    /**
     * @var bool
     */
    protected bool $backButton = false;

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
        if($this->target()->method() === 'fund')
        {
            $fundId = $this->target()->args()[0];
            $this->bag('report')->set('fund.id', $fundId);
        }
        $this->fund = $this->fundService->getFund($fundId, true, true);
    }

    /**
     * @exclude
     *
     * @return array
     */
    public function getData(): array
    {
        return [$this->savings, $this->session, $this->fund, $this->profitAmount];
    }

    protected function before()
    {
        if($this->profitAmount === null)
        {
            $this->profitAmount = $this->closingService
                ->getProfitAmount($this->session, $this->fund);
        }
        $this->savings = $this->profitService
            ->getDistributions($this->session, $this->fund, $this->profitAmount);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-fund-savings-page');
    }

    public function html(): string
    {
        return (string)$this->renderView('pages.report.session.savings.fund', [
            'fund' => $this->fund,
            'profitAmount' => $this->profitAmount,
            'backButton' => $this->backButton,
        ]);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, FundModel $fund): ComponentResponse
    {
        $this->bag('report')->set('session.id', $session->id);
        $this->bag('report')->set('fund.id', $fund->id);
        $this->session = $session;
        $this->fund = $fund;
        $this->backButton = true;

        return $this->render();
    }

    public function fund(int $fundId)
    {
        return $this->render();
    }

    public function amount(int $profitAmount)
    {
        $this->profitAmount = $profitAmount;
        $this->before();

        $this->cl(Amount::class)->render();
        $this->cl(Summary::class)->render();
        $this->cl(Distribution::class)->render();

        return $this->response;
    }
}
