<?php

namespace App\Ajax\App\Meeting\Pool;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Cash\Disbursement;
use App\Ajax\App\Meeting\Credit\Loan;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @databag auction
 * @before getSession
 */
class Auction extends CallableClass
{
    /**
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @var RefundService
     */
    protected RefundService $refundService;

    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param TontineService $tontineService
     * @param RefundService $refundService
     */
    public function __construct(TontineService $tontineService, RefundService $refundService)
    {
        $this->tontineService = $tontineService;
        $this->refundService = $refundService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->refundService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        if(!$this->tontineService->hasPoolWithAuction())
        {
            return $this->response;
        }
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.auction.home')
            ->with('session', $this->session);
        $this->response->html('meeting-auctions', $html);
        $this->jq('#btn-auctions-refresh')->click($this->rq()->home());
        $this->jq('#btn-auctions-filter')->click($this->rq()->toggleFilter());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $filtered = $this->bag('auction')->get('filter', null);
        $debtCount = $this->refundService->getAuctionCount($this->session, $filtered);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $debtCount, 'auction');
        $debts = $this->refundService->getAuctions($this->session, $filtered, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $debtCount);

        $html = $this->view()->render('tontine.pages.meeting.auction.page', [
            'session' => $this->session,
            'debts' => $debts,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-auctions-page', $html);

        $debtId = jq()->parent()->attr('data-debt-id')->toInt();
        $this->jq('.btn-add-refund', '#meeting-auctions-page')->click($this->rq()->createRefund($debtId));
        $this->jq('.btn-del-refund', '#meeting-auctions-page')->click($this->rq()->deleteRefund($debtId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('auction')->set('filter', $filtered);

        return $this->page(1);
    }

    /**
     * @di $validator
     */
    public function createRefund(string $debtId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->validator->validate($debtId);
        $this->refundService->createRefund($this->session, $debtId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    public function deleteRefund(int $debtId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->refundService->deleteRefund($this->session, $debtId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }
}
