<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Fund as FundModel;
use App\Ajax\CallableClass;

use function jq;

/**
 * @databag meeting
 * @before getSession
 */
class Bidding extends CallableClass
{
    /**
     * @di
     * @var BiddingService
     */
    protected BiddingService $biddingService;

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
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->biddingService->getSession($sessionId);
    }

    /**
     * @return void
     */
    protected function getFund()
    {
        $fundId = $this->target()->method() === 'fund' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fund.id');
        $this->fund = $this->biddingService->getFund($fundId);
    }

    /**
     * @before getFund
     */
    public function fund($fundId)
    {
        $this->bag('meeting')->set('fund.id', $fundId);

        $figures = $this->biddingService->getRemittanceFigures($this->fund, $this->session->id);
        $biddings = $figures->payables
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

        $html = $this->view()->render('pages.meeting.bidding.home')->with('fund', $this->fund)
            ->with('biddings', $biddings)->with('session', $this->session);
        $this->response->html('meeting-funds', $html);

        $this->jq('#btn-biddings-back')->click($this->cl(Fund::class)->rq()->home());
        $this->jq('.btn-bidding-add')->click($this->rq()->addRemittance());
        $subscriptionId = jq()->parent()->attr('data-subscription-id');
        $this->jq('.btn-bidding-delete')->click($this->rq()->deleteRemittance($subscriptionId));

        return $this->response;
    }

    /**
     * @before getFund
     */
    public function addRemittance()
    {
        $members = $this->biddingService->getSubscriptions($this->fund);
        $title = trans('tontine.bidding.titles.add');
        $content = $this->view()->render('pages.meeting.bidding.add-remittance')
            ->with('members', $members);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemittance(pm()->form('bidding-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @before getFund
     */
    public function saveRemittance(array $formValues)
    {
        $subscriptionId = $formValues['subscription'];
        $amountPaid = $formValues['amount'];
        $this->biddingService->createRemittance($this->fund, $this->session, $subscriptionId, $amountPaid);
        $this->dialog->hide();
        // $this->notify->success(trans('session.remittance.created'), trans('common.titles.success'));

        return $this->fund($this->fund->id);
    }

    /**
     * @before getFund
     */
    public function deleteRemittance($subscriptionId)
    {
        $this->biddingService->deleteRemittance($this->fund, $this->session, $subscriptionId);
        // $this->notify->success(trans('session.remittance.deleted'), trans('common.titles.success'));

        return $this->fund($this->fund->id);
    }

    public function cash()
    {
        $biddings = $this->biddingService->getSessionBiddings($this->session);

        $html = $this->view()->render('pages.meeting.bidding.home')
            ->with('biddings', $biddings)->with('session', $this->session);
        $this->response->html('meeting-funds', $html);

        $this->jq('#btn-biddings-back')->click($this->cl(Fund::class)->rq()->home());
        $this->jq('.btn-bidding-add')->click($this->rq()->addBidding());
        $biddingId = jq()->parent()->attr('data-bidding-id');
        $this->jq('.btn-bidding-delete')->click($this->rq()->deleteBidding($biddingId));

        return $this->response;
    }

    public function addBidding()
    {
        $amountAvailable = $this->biddingService->getAmountAvailable($this->session);
        if($amountAvailable <= 0)
        {
            $this->notify->success(trans('session.bidding.errors.amount'), trans('common.titles.warning'));
            return $this->response;
        }

        $members = $this->biddingService->getMembers();
        $title = trans('tontine.bidding.titles.add');
        $content = $this->view()->render('pages.meeting.bidding.add-cash')
            ->with('members', $members)->with('amount', $amountAvailable);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveBidding(pm()->form('bidding-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function saveBidding(array $formValues)
    {
        $member = $this->biddingService->getMember(intval($formValues['member']));
        $this->biddingService->createBidding($this->session, $member,
            intval($formValues['amount_bid']), intval($formValues['amount_paid']));
        $this->dialog->hide();
        // $this->notify->success(trans('session.remittance.created'), trans('common.titles.success'));

        return $this->cash();
    }

    public function deleteBidding($biddingId)
    {

        return $this->response;
    }
}
