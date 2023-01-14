<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Service\Meeting\PoolService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Session extends CallableClass
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
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SessionService
     */
    protected SessionService $sessionService;

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
        $this->session = $this->poolService->getSession($sessionId);
    }

    public function home(int $sessionId)
    {
        $this->bag('meeting')->set('session.id', $sessionId);

        return $this->pools();
    }

    public function pools()
    {
        $html = $this->view()->render('tontine.pages.meeting.session.pools', [
            'tontine' => $this->poolService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Meeting::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->pools());
        $this->jq('#btn-session-loans')->click($this->rq()->loans());
        $this->jq('#btn-session-charges')->click($this->rq()->charges());
        $this->jq('#btn-session-open')->click($this->rq()->open()
            ->confirm(trans('tontine.session.questions.open')));
        $this->jq('#btn-session-close')->click($this->rq()->close()
            ->confirm(trans('tontine.session.questions.close')));
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda(pm()->input('text-session-agenda')));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport(pm()->input('text-session-report')));

        $this->cl(Pool::class)->show($this->session, $this->poolService);

        return $this->response;
    }

    /**
     * @di $loanService
     * @di $refundService
     */
    public function loans()
    {
        $html = $this->view()->render('tontine.pages.meeting.session.loans', [
            'tontine' => $this->poolService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->loans());
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
        $html = $this->view()->render('tontine.pages.meeting.session.charges', [
            'tontine' => $this->poolService->getTontine(),
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->charges());
        $this->jq('#btn-session-pools')->click($this->rq()->pools());
        $this->jq('#btn-session-loans')->click($this->rq()->loans());
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
        $this->sessionService->openSession($this->session);
        $this->home($this->session->id);

        return $this->response;
    }

    public function close()
    {
        $this->sessionService->closeSession($this->session);
        $this->home($this->session->id);

        return $this->response;
    }

    public function saveAgenda(string $text)
    {
        $this->sessionService->saveAgenda($this->session, $text);
        $this->notify->success(trans('meeting.messages.agenda.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $this->sessionService->saveReport($this->session, $text);
        $this->notify->success(trans('meeting.messages.report.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
