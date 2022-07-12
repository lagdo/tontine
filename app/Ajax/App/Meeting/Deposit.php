<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\DepositService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Fund as FundModel;
use App\Ajax\CallableClass;

use function jq;
use function trans;

/**
 * @databag meeting
 * @before getFund
 */
class Deposit extends CallableClass
{
    /**
     * @di
     * @var DepositService
     */
    protected DepositService $depositService;

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
        $this->session = $this->depositService->getSession($sessionId);
        $this->fund = $this->depositService->getFund($fundId);
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

        $html = $this->view()->render('pages.meeting.deposit.home', [
            'fund' => $this->fund,
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->jq('#btn-deposits-back')->click($this->cl(Fund::class)->rq()->deposits());

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
            $pageNumber = $this->bag('meeting')->get('deposit.page', 1);
        }
        $this->bag('meeting')->set('deposit.page', $pageNumber);

        $receivableCount = $this->depositService->getReceivableCount($this->fund, $this->session);
        $html = $this->view()->render('pages.meeting.deposit.page', [
            'receivables' => $this->depositService->getReceivables($this->fund, $this->session, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $receivableCount),
        ]);
        $this->response->html('meeting-fund-deposits', $html);

        $receivableId = jq()->parent()->attr('data-receivable-id');
        $this->jq('.btn-add-deposit')->click($this->rq()->addDeposit($receivableId));
        $this->jq('.btn-del-deposit')->click($this->rq()->delDeposit($receivableId));
        $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($receivableId));

        return $this->response;
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit($receivableId)
    {
        $this->depositService->createDeposit($this->fund, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.created'), trans('common.titles.success'));

        return $this->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function delDeposit($receivableId)
    {
        $this->depositService->deleteDeposit($this->fund, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
