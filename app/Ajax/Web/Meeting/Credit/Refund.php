<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @databag refund
 * @before getSession
 */
class Refund extends CallableClass
{
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
     * @param SessionService $sessionService
     * @param RefundService $refundService
     */
    public function __construct(protected SessionService $sessionService,
        protected RefundService $refundService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
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
        $html = $this->render('pages.meeting.refund.home')
            ->with('session', $this->session);
        $this->response->html('meeting-refunds', $html);
        $this->jq('#btn-refunds-refresh')->click($this->rq()->home());
        $this->jq('#btn-refunds-filter')->click($this->rq()->toggleFilter());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $filtered = $this->bag('refund')->get('filter', null);
        $debtCount = $this->refundService->getDebtCount($this->session, $filtered);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $debtCount, 'refund', 'principal.page');
        $debts = $this->refundService->getDebts($this->session, $filtered, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $debtCount);

        $html = $this->render('pages.meeting.refund.page', [
            'session' => $this->session,
            'debts' => $debts,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-debts-page', $html);

        $debtId = jq()->parent()->attr('data-debt-id')->toInt();
        $this->jq('.btn-add-refund', '#meeting-debts-page')->click($this->rq()->createRefund($debtId));
        $this->jq('.btn-del-refund', '#meeting-debts-page')->click($this->rq()->deleteRefund($debtId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('filter', $filtered);

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
