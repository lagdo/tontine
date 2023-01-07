<?php

namespace App\Ajax\App\Meeting\Financial;

use Siak\Tontine\Service\RefundService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Refund extends CallableClass
{
    /**
     * @di
     * @var RefundService
     */
    protected RefundService $refundService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

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
    public function show($session, $refundService)
    {
        $this->session = $session;
        $this->refundService = $refundService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('pages.meeting.refund.home')
            ->with('session', $this->session);
        $this->response->html('meeting-refunds', $html);
        $this->jq('#btn-refunds-refresh')->click($this->rq()->home());
        $this->jq('#btn-refunds-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('meeting')->get('loan.page', 1);
        }
        $this->bag('meeting')->set('loan.page', $pageNumber);

        $refunded = $this->bag('meeting')->get('loan.filter', null);
        $loanCount = $this->refundService->getLoanCount($this->session, $refunded);
        $html = $this->view()->render('pages.meeting.refund.page', [
            'session' => $this->session,
            'loans' => $this->refundService->getLoans($this->session, $refunded, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $loanCount),
        ]);
        if($this->session->closed)
        {
            $html->with('refundSum', $this->refundService->getRefundSum($this->session));
        }
        $this->response->html('meeting-loans-page', $html);

        $loanId = jq()->parent()->attr('data-loan-id')->toInt();
        $this->jq('.btn-add-refund')->click($this->rq()->create($loanId));
        $refundId = jq()->parent()->attr('data-refund-id')->toInt();
        $this->jq('.btn-del-refund')->click($this->rq()->delete($refundId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $refunded = $this->bag('meeting')->get('loan.filter', null);
        // Switch between null, true and false
        $refunded = $refunded === null ? true : ($refunded === true ? false : null);
        $this->bag('meeting')->set('loan.filter', $refunded);

        return $this->page(1);
    }

    public function create(int $loanId)
    {
        $this->refundService->createRefund($this->session, $loanId);

        return $this->page();
    }

    public function delete(int $refundId)
    {
        $this->refundService->deleteRefund($this->session, $refundId);

        return $this->page();
    }
}
