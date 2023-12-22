<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class FixedFee extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var FixedFeeService
     */
    protected FixedFeeService $feeService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * The constructor
     *
     * @param FixedFeeService $feeService
     */
    public function __construct(FixedFeeService $feeService)
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
        $html = $this->render('pages.meeting.charge.fixed.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-fixed', $html);
        $this->jq('#btn-fees-fixed-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $chargeCount = $this->feeService->getFeeCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $chargeCount,
            'meeting', 'fee.fixed.page');
        $charges = $this->feeService->getFees($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $chargeCount);
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);

        $html = $this->render('pages.meeting.charge.fixed.page')
            ->with('session', $this->session)
            ->with('charges', $charges)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('pagination', $pagination);
        $this->response->html('meeting-fees-fixed-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-fee-fixed-settlements')
            ->click($this->cl(Fixed\Settlement::class)->rq()->home($chargeId));

        return $this->response;
    }
}
