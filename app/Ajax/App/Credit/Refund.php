<?php

namespace App\Ajax\App\Credit;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

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
        $sessionId = $this->bag('meeting')->get('session.id');
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
            $pageNumber = $this->bag('meeting')->get('debt.page', 1);
        }
        $this->bag('meeting')->set('debt.page', $pageNumber);

        $refunded = $this->bag('meeting')->get('debt.filter', null);
        $debtCount = $this->refundService->getDebtCount($this->session, $refunded);
        $html = $this->view()->render('tontine.pages.meeting.refund.page', [
            'debts' => $this->refundService->getDebts($this->session, $refunded, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $debtCount),
        ]);
        $this->response->html('meeting-debts-page', $html);

        $debtId = jq()->parent()->attr('data-debt-id');
        $this->jq('.btn-add-refund')->click($this->rq()->createRefund($debtId));
        $refundId = jq()->parent()->attr('data-refund-id')->toInt();
        $this->jq('.btn-del-refund')->click($this->rq()->deleteRefund($refundId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $refunded = $this->bag('meeting')->get('debt.filter', null);
        // Switch between null, true and false
        $refunded = $refunded === null ? true : ($refunded === true ? false : null);
        $this->bag('meeting')->set('debt.filter', $refunded);

        return $this->page(1);
    }

    /**
     * @di $validator
     */
    public function createRefund(string $debtId)
    {
        $values = $this->validator->validate($debtId);

        $this->refundService->createRefund($this->session, $values['loan_id'], $values['type']);

        return $this->page();
    }

    public function deleteRefund(int $refundId)
    {
        $this->refundService->deleteRefund($this->session, $refundId);

        return $this->page();
    }
}
