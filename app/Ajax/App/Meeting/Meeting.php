<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Meeting extends CallableClass
{
    /**
     * @var FeeService
     */
    protected FeeService $feeService;

    /**
     * @var FineService
     */
    protected FineService $fineService;

    /**
     * @var FeeReportService
     */
    protected FeeReportService $feeReportService;

    /**
     * @var FineReportService
     */
    protected FineReportService $fineReportService;

    /**
     * @var LoanService
     */
    protected LoanService $loanService;

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

        return $this->pools();
    }

    public function pools()
    {
        $html = $this->view()->render('pages.meeting.session.pools', [
            'tontine' => $this->meetingService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->pools());
        $this->jq('#btn-session-bids')->click($this->rq()->bids());
        $this->jq('#btn-session-charges')->click($this->rq()->charges());
        $this->jq('#btn-session-open')->click($this->rq()->open()
            ->confirm(trans('tontine.session.questions.open')));
        $this->jq('#btn-session-close')->click($this->rq()->close()
            ->confirm(trans('tontine.session.questions.close')));
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Pool::class)->show($this->session, $this->meetingService);

        return $this->response;
    }

    /**
     * @di $loanService
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
        $this->jq('#btn-session-pools')->click($this->rq()->pools());
        $this->jq('#btn-session-charges')->click($this->rq()->charges());
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Financial\Loan::class)->show($this->session, $this->loanService);
        $this->cl(Financial\Refund::class)->show($this->session, $this->refundService);

        return $this->response;
    }

    /**
     * @di $feeService
     * @di $fineService
     * @di $feeReportService
     * @di $fineReportService
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
        $this->jq('#btn-session-pools')->click($this->rq()->pools());
        $this->jq('#btn-session-bids')->click($this->rq()->bids());
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Charge\Fee::class)->show($this->session, $this->feeService, $this->feeReportService);
        $this->cl(Charge\Fine::class)->show($this->session, $this->fineService, $this->fineReportService);

        return $this->response;
    }

    public function report()
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
