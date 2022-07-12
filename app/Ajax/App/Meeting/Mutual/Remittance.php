<?php

namespace App\Ajax\App\Meeting\Mutual;

use App\Ajax\App\Meeting\Fund;
use App\Ajax\CallableClass;
use Siak\Tontine\Service\RemittanceService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Fund as FundModel;

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
        // Get session
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->remittanceService->getSession($sessionId);
        // Get fund
        $fundId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fund.id');
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

        $payables = $this->remittanceService->getPayables($this->fund, $this->session);
        $html = $this->view()->render('pages.meeting.remittance.mutual', [
            'fund' => $this->fund,
            'payables' => $payables,
        ]);
        $this->response->html('meeting-remittances', $html);
        $this->jq('#btn-remittances-back')->click($this->cl(Fund::class)->rq()->remittances());
        $payableId = jq()->parent()->attr('data-payable-id');
        $this->jq('.btn-add-remittance')->click($this->rq()->addRemittance($payableId));
        $this->jq('.btn-del-remittance')->click($this->rq()->delRemittance($payableId));

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

        return $this->home($this->fund->id);
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

        return $this->home($this->fund->id);
    }
}
