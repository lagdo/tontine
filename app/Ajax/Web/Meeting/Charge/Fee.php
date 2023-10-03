<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FeeService;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Fee extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var FeeService
     */
    protected FeeService $feeService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * The constructor
     *
     * @param FeeService $feeService
     */
    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->feeService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.charge.fixed.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees', $html);
        $this->jq('#btn-fees-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $feeCount = $this->feeService->getFeeCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $feeCount, 'meeting', 'fee.page');
        $fees = $this->feeService->getFees($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $feeCount);
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);
        foreach($fees as $fee)
        {
            $fee->currentBillCount = ($bills['total']['current'][$fee->id] ?? 0);
            if(!$fee->period_session)
            {
                $fee->currentBillCount -= ($settlements['total']['previous'][$fee->id] ?? 0);
            }
            $fee->previousBillCount = ($bills['total']['previous'][$fee->id] ?? 0);
        }

        $html = $this->view()->render('tontine.pages.meeting.charge.fixed.page')
            ->with('session', $this->session)
            ->with('fees', $fees)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('pagination', $pagination);
        $this->response->html('meeting-fees-page', $html);

        $feeId = jq()->parent()->attr('data-fee-id')->toInt();
        $this->jq('.btn-fee-settlements')->click($this->cl(Settlement\Fee::class)->rq()->home($feeId));

        return $this->response;
    }
}
