<?php

namespace App\Ajax\App\Meeting\Financial;

use App\Ajax\App\Meeting\Fund;
use App\Ajax\CallableClass;
use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Validation\Meeting\RemittanceValidator;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Fund as FundModel;

use function jq;

/**
 * @databag meeting
 * @before getFund
 */
class Remittance extends CallableClass
{
    /**
     * @di
     * @var BiddingService
     */
    protected BiddingService $biddingService;

    /**
     * @var RemittanceValidator
     */
    protected RemittanceValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund = null;

    /**
     * @return void
     */
    protected function getFund()
    {
        // Get session
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->biddingService->getSession($sessionId);
        // Get fund
        $fundId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fund.id');
        $this->fund = $this->biddingService->getFund($fundId);
        if($this->session->disabled($this->fund))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->fund = null;
        }
    }

    public function home($fundId)
    {
        $this->bag('meeting')->set('fund.id', $fundId);

        $figures = $this->biddingService->getRemittanceFigures($this->fund, $this->session->id);
        $payables = $figures->payables
            ->map(function($payable) use($figures) {
                return (object)[
                    'id' => $payable->subscription->id,
                    'title' => $payable->subscription->member->name,
                    'amount' => $figures->amount,
                    'paid' => 0,
                    'available' => false,
                ];
            })->pad($figures->count, (object)[
                'id' => 0,
                'title' => '** ' . trans('figures.bidding.titles.available') . ' **',
                'amount' => $figures->amount,
                'paid' => 0,
                'available' => true,
            ]);

        $html = $this->view()->render('pages.meeting.remittance.financial')
            ->with('fund', $this->fund)
            ->with('session', $this->session)
            ->with('payables', $payables);
        $this->response->html('meeting-remittances', $html);

        $this->jq('#btn-remittances-back')->click($this->cl(Fund::class)->rq()->remittances());
        $this->jq('.btn-add-remittance')->click($this->rq()->addRemittance());
        $subscriptionId = jq()->parent()->attr('data-subscription-id')->toInt();
        $this->jq('.btn-del-remittance')->click($this->rq()->deleteRemittance($subscriptionId));

        return $this->response;
    }

    public function addRemittance()
    {
        $members = $this->biddingService->getSubscriptions($this->fund);
        $title = trans('tontine.bidding.titles.add');
        $content = $this->view()->render('pages.meeting.remittance.add')
            ->with('members', $members);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemittance(pm()->form('remittance-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveRemittance(array $formValues)
    {
        $this->validator->validateItem($formValues);

        $subscriptionId = $formValues['subscription'];
        $amountPaid = $formValues['amount'];
        $this->biddingService->createRemittance($this->fund, $this->session, $subscriptionId, $amountPaid);
        $this->dialog->hide();
        // $this->notify->success(trans('session.remittance.created'), trans('common.titles.success'));

        return $this->home($this->fund->id);
    }

    public function deleteRemittance(int $subscriptionId)
    {
        $this->biddingService->deleteRemittance($this->fund, $this->session, $subscriptionId);
        // $this->notify->success(trans('session.remittance.deleted'), trans('common.titles.success'));

        return $this->home($this->fund->id);
    }
}
