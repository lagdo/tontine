<?php

namespace App\Ajax\App\Charge;

use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

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
     * @di
     * @var FeeService
     */
    protected FeeService $feeService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

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
    public function show(SessionModel $session, FeeService $feeService)
    {
        $this->session = $session;
        $this->feeService = $feeService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.fee.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees', $html);
        $this->jq('#btn-fees-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fees = $this->feeService->getFees($pageNumber);
        $feeCount = $this->feeService->getFeeCount();
        // Bill and settlement counts and amounts
        [$bills, $settlements] = $this->feeService->getBills($this->session);

        $html = $this->view()->render('tontine.pages.meeting.fee.page')
            ->with('session', $this->session)
            ->with('fees', $fees)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('zero', $settlements['zero'])
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $feeCount));
        $this->response->html('meeting-fees-page', $html);

        $feeId = jq()->parent()->attr('data-fee-id')->toInt();
        $this->jq('.btn-fee-settlements')->click($this->cl(Settlement\Fee::class)->rq()->home($feeId));

        return $this->response;
    }
}
