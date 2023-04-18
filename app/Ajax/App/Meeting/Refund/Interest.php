<?php

namespace App\Ajax\App\Meeting\Refund;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Credit\Loan;
use Siak\Tontine\Model\Refund as RefundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function trans;

/**
 * @databag refund
 * @before getSession
 */
class Interest extends CallableClass
{
    /**
     * @di
     * @var RefundService
     */
    protected RefundService $refundService;

    /**
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('refund')->get('session.id');
        $this->session = $this->refundService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, RefundService $refundService)
    {
        $this->session = $session;
        $this->refundService = $refundService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.refund.home')
            ->with('session', $this->session)
            ->with('type', 'interest');
        $this->response->html('meeting-interest-refunds', $html);
        $this->jq('#btn-interest-refunds-refresh')->click($this->rq()->home());
        $this->jq('#btn-interest-refunds-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $filtered = $this->bag('refund')->get('interest.filter', null);
        $debtCount = $this->refundService->getInterestDebtCount($this->session, $filtered);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $debtCount, 'refund', 'interest.page');
        $debts = $this->refundService->getInterestDebts($this->session, $filtered, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $debtCount);

        $html = $this->view()->render('tontine.pages.meeting.refund.page', [
            'session' => $this->session,
            'debts' => $debts,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-interest-debts-page', $html);

        $debtId = jq()->parent()->attr('data-debt-id');
        $this->jq('.btn-add-interest-refund')->click($this->rq()->createRefund($debtId));
        $refundId = jq()->parent()->attr('data-refund-id')->toInt();
        $this->jq('.btn-del-interest-refund')->click($this->rq()->deleteRefund($refundId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('interest.filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('interest.filter', $filtered);

        return $this->page(1);
    }

    /**
     * @di $validator
     * @di $loanService
     */
    public function createRefund(string $loanId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->validator->validate($loanId);

        $this->refundService->createRefund($this->session, $loanId, RefundModel::TYPE_INTEREST);

        // Refresh the loans page
        $this->cl(Loan::class)->show($this->session, $this->loanService);

        return $this->page();
    }

    /**
     * @di $loanService
     */
    public function deleteRefund(int $refundId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->refundService->deleteRefund($this->session, $refundId);

        // Refresh the loans page
        $this->cl(Loan::class)->show($this->session, $this->loanService);

        return $this->page();
    }
}
