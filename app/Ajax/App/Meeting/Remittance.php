<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\RemittanceService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Fund as FundModel;
use App\Ajax\CallableClass;

use function jq;
use function trans;

/**
 * @databag meeting
 * @before getFund
 */
class Remittance extends CallableClass
{
    /**
     * @di
     * @var RemittanceService
     */
    protected RemittanceService $remittanceService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund;

    protected function getFund()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $fundId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fund.id');
        $this->session = $this->remittanceService->getSession($sessionId);
        $this->fund = $this->remittanceService->getFund($fundId);
        if($this->session->disabled($this->fund))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->fund = null;
        }
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function home(int $fundId)
    {
        $this->bag('meeting')->set('fund.id', $fundId);

        $html = $this->view()->render('pages.meeting.remittance.home', [
            'fund' => $this->fund,
        ]);
        $this->response->html('meeting-funds', $html);
        $this->jq('#btn-remittances-back')->click($this->cl(Fund::class)->rq()->home());

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
            $pageNumber = $this->bag('meeting')->get('remittance.page', 1);
        }
        $this->bag('meeting')->set('remittance.page', $pageNumber);

        $payableCount = $this->remittanceService->getPayableCount($this->fund, $this->session);
        $html = $this->view()->render('pages.meeting.remittance.page', [
            'payables' => $this->remittanceService->getPayables($this->fund, $this->session, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $payableCount),
        ]);
        $this->response->html('meeting-fund-remittances', $html);

        $payableId = jq()->parent()->attr('data-payable-id');
        $this->jq('.btn-add-remittance')->click($this->rq()->addRemittance($payableId));
        $this->jq('.btn-del-remittance')->click($this->rq()->delRemittance($payableId));
        $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($payableId));

        return $this->response;
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function addRemittance($payableId)
    {
        $this->remittanceService->createRemittance($this->fund, $this->session, $payableId);
        // $this->notify->success(trans('session.remittance.created'), trans('common.titles.success'));

        return $this->page();
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function delRemittance($payableId)
    {
        $this->remittanceService->deleteRemittance($this->fund, $this->session, $payableId);
        // $this->notify->success(trans('session.remittance.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
