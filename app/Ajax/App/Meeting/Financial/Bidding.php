<?php

namespace App\Ajax\App\Meeting\Financial;

use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Validation\Meeting\BiddingValidator;
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
     * @var BiddingValidator
     */
    protected BiddingValidator $validator;

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
        [$biddings, $sum] = $this->biddingService->getSessionBiddings($this->session);
        $amountAvailable = $this->biddingService->getAmountAvailable($this->session);

        $html = $this->view()->render('pages.meeting.bidding.home')
            ->with('biddings', $biddings)->with('session', $this->session)
            ->with('amountAvailable', Currency::format($amountAvailable));
        if($this->session->closed)
        {
            $html->with('sum', $sum);
        }
        $this->response->html('meeting-biddings', $html);

        $this->jq('#btn-biddings-refresh')->click($this->rq()->home());
        $this->jq('.btn-bidding-add')->click($this->rq()->addBidding());
        $biddingId = jq()->parent()->attr('data-subscription-id')->toInt();
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

    /**
     * @di $validator
     */
    public function saveBidding(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $member = $this->biddingService->getMember($values['member']);
        $this->biddingService->createBidding($this->session, $member,
            $values['amount_bid'], $values['amount_paid']);
        $this->dialog->hide();

        return $this->home();
    }

    public function deleteBidding(int $biddingId)
    {
        $this->biddingService->deleteBidding($this->session, $biddingId);

        return $this->home();
    }
}
