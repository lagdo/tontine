<?php

namespace App\Ajax\Web\Meeting\Credit;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag partial.refund
 */
class PartialRefund extends CallableSessionClass
{
    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * The constructor
     *
     * @param RefundService $refundService
     */
    public function __construct(protected RefundService $refundService)
    {}

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
        $html = $this->render('pages.meeting.refund.partial.home')
            ->with('session', $this->session);
        $this->response->html('meeting-partial-refunds', $html);
        $this->jq('#btn-partial-refunds-refresh')->click($this->rq()->home());
        $this->jq('#btn-partial-refunds-add')->click($this->rq()->addRefund());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $refundCount = $this->refundService->getPartialRefundCount($this->session);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $refundCount,
            'partial.refund', 'principal.page');
        $refunds = $this->refundService->getPartialRefunds($this->session, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $refundCount);

        $html = $this->render('pages.meeting.refund.partial.page', [
            'session' => $this->session,
            'refunds' => $refunds,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-partial-refunds-page', $html);

        $refundId = jq()->parent()->attr('data-refund-id')->toInt();
        $this->jq('.btn-del-partial-refund')->click($this->rq()->deleteRefund($refundId)
            ->confirm(trans('meeting.refund.questions.delete')));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function addRefund()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $title = trans('meeting.refund.titles.add');
        $content = $this->render('pages.meeting.refund.partial.add')
            ->with('debts', $this->refundService->getUnpaidDebtList($this->session));
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createRefund(pm()->form('refund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @after showBalanceAmounts
     */
    public function createRefund(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $debtId = $values['debt'];
        $amount = $values['amount'];
        $this->refundService->createPartialRefund($this->session, $debtId, $amount);

        $this->dialog->hide();

        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);

        return $this->home();
    }

    /**
     * @after showBalanceAmounts
     */
    public function deleteRefund(int $refundId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->refundService->deletePartialRefund($this->session, $refundId);

        // Refresh the refunds pages
        $this->cl(Refund::class)->show($this->session);

        return $this->home();
    }
}
