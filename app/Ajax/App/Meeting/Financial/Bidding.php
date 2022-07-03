<?php

namespace App\Ajax\App\Meeting\Financial;

use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Session as SessionModel;
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
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->biddingService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show($session, $biddingService)
    {
        $this->session = $session;
        $this->biddingService = $biddingService;

        return $this->home();
    }

    public function home()
    {
        $biddings = $this->biddingService->getSessionBiddings($this->session);
        $amountAvailable = $this->biddingService->getAmountAvailable($this->session);

        $html = $this->view()->render('pages.meeting.bidding.home')
            ->with('biddings', $biddings)->with('session', $this->session)
            ->with('amountAvailable', Currency::format($amountAvailable));
        $this->response->html('meeting-biddings', $html);

        $this->jq('#btn-biddings-refresh')->click($this->rq()->home());
        $this->jq('.btn-bidding-add')->click($this->rq()->addBidding());
        $biddingId = jq()->parent()->attr('data-subscription-id');
        $this->jq('.btn-bidding-delete')->click($this->rq()->deleteBidding($biddingId));

        return $this->response;
    }

    public function addBidding()
    {
        $amountAvailable = $this->biddingService->getAmountAvailable($this->session);
        if($amountAvailable <= 0)
        {
            return $this->response;
        }

        $members = $this->biddingService->getMembers();
        $title = trans('tontine.bidding.titles.add');
        $content = $this->view()->render('pages.meeting.bidding.add')
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

        return $this->home();
    }

    public function deleteBidding($biddingId)
    {
        $this->biddingService->deleteBidding($this->session, intval($biddingId));

        return $this->home();
    }
}
