<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Service\RefundService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function intval;
use function jq;
use function pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Meeting extends CallableClass
{
    /**
     * @di
     * @var MeetingService
     */
    protected MeetingService $meetingService;

    /**
     * @var FeeSettlementService
     */
    protected FeeSettlementService $feeSettlementService;

    /**
     * @var FineSettlementService
     */
    protected FineSettlementService $fineSettlementService;

    /**
     * @var BiddingService
     */
    protected BiddingService $biddingService;

    /**
     * @var RefundService
     */
    protected RefundService $refundService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('session.id');
        $this->session = $this->meetingService->getSession($sessionId);
    }

    public function home(int $sessionId)
    {
        $this->bag('meeting')->set('session.id', $sessionId);

        return $this->funds();

        // $tontine = $this->meetingService->getTontine();
        // $html = $this->view()->render('pages.meeting.session.funds',
        //     ['tontine' => $tontine, 'session' => $this->session]);
        // $this->response->html('content-home', $html);

        // $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        // $this->jq('#btn-session-refresh')->click($this->rq()->home($sessionId));
        // $this->jq('#btn-session-open')->click($this->rq()->open()
        //     ->confirm(trans('tontine.session.questions.open')));
        // $this->jq('#btn-session-close')->click($this->rq()->close()
        //     ->confirm(trans('tontine.session.questions.close')));
        // $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        // $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        // $this->cl(Fund::class)->show($this->session, $this->meetingService);
        // $this->cl(Charge::class)->show($this->session, $this->meetingService);
        // if($tontine->is_financial)
        // {
        //     $this->cl(Financial\Bidding::class)->show($this->session, $this->biddingService);
        //     $this->cl(Financial\Refund::class)->show($this->session, $this->refundService);
        // }

        return $this->response;
    }

    public function funds()
    {
        $html = $this->view()->render('pages.meeting.session.funds', [
            'tontine' => $this->meetingService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->funds());
        $this->jq('#btn-session-bids')->click($this->rq()->bids());
        $this->jq('#btn-session-charges')->click($this->rq()->charges());
        $this->jq('#btn-session-open')->click($this->rq()->open()
            ->confirm(trans('tontine.session.questions.open')));
        $this->jq('#btn-session-close')->click($this->rq()->close()
            ->confirm(trans('tontine.session.questions.close')));
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Fund::class)->show($this->session, $this->meetingService);

        return $this->response;
    }

    /**
     * @di $biddingService
     * @di $refundService
     */
    public function bids()
    {
        $html = $this->view()->render('pages.meeting.session.bids', [
            'tontine' => $this->meetingService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->bids());
        $this->jq('#btn-session-funds')->click($this->rq()->funds());
        $this->jq('#btn-session-charges')->click($this->rq()->charges());
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Financial\Bidding::class)->show($this->session, $this->biddingService);
        $this->cl(Financial\Refund::class)->show($this->session, $this->refundService);

        return $this->response;
    }

    /**
     * @di $feeSettlementService
     * @di $fineSettlementService
     */
    public function charges()
    {
        $html = $this->view()->render('pages.meeting.session.charges', [
            'tontine' => $this->meetingService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->charges());
        $this->jq('#btn-session-funds')->click($this->rq()->funds());
        $this->jq('#btn-session-bids')->click($this->rq()->bids());
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Charge\Fee::class)->show($this->session, $this->meetingService, $this->feeSettlementService);
        $this->cl(Charge\Fine::class)->show($this->session, $this->meetingService, $this->fineSettlementService);

        return $this->response;
    }

    public function summary()
    {
        return $this->response;
    }

    public function open()
    {
        $this->session->update(['status' => SessionModel::STATUS_OPENED]);

        $this->home($this->session->id);

        return $this->response;
    }

    public function close()
    {
        $this->session->update(['status' => SessionModel::STATUS_CLOSED]);

        $this->home($this->session->id);

        return $this->response;
    }

    public function saveAgenda(string $text)
    {
        $this->meetingService->updateSessionAgenda($this->session, $text);
        $this->notify->success(trans('meeting.messages.agenda.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $this->meetingService->updateSessionReport($this->session, $text);
        $this->notify->success(trans('meeting.messages.report.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
