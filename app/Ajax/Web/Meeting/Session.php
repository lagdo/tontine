<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use App\Ajax\Web\Meeting\Charge\FixedFee;
use App\Ajax\Web\Meeting\Charge\LibreFee;
use App\Ajax\Web\Meeting\Credit\Loan;
use App\Ajax\Web\Meeting\Credit\PartialRefund;
use App\Ajax\Web\Meeting\Credit\Refund;
use App\Ajax\Web\Meeting\Pool\Deposit;
use App\Ajax\Web\Meeting\Pool\Remitment;
use App\Ajax\Web\Meeting\Saving\Closing;
use App\Ajax\Web\Meeting\Saving\Saving;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\TontineService;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 */
class Session extends CallableClass
{
    /**
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @di
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
    protected function getSessionFromArgs()
    {
        $sessionId = $this->target()->args()[0];
        $this->session = $this->sessionService->getSession($sessionId);
        $this->bag('meeting')->set('session.id', $sessionId);
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
    }

    /**
     * @di $tontineService
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->render('pages.meeting.session.list');
        $this->response->html('content-home', $html);

        $this->jq('#btn-sessions-refresh')->click($this->rq()->page());

        return $this->page();
    }

    /**
     * @di $tontineService
     */
    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount, 'session', 'page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $statuses = [
            SessionModel::STATUS_PENDING => trans('tontine.session.status.pending'),
            SessionModel::STATUS_OPENED => trans('tontine.session.status.opened'),
            SessionModel::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];
        $html = $this->render('pages.meeting.session.page')
            ->with('sessions', $sessions)
            ->with('statuses', $statuses)
            ->with('members', $this->tontineService->getMembers())
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-show')->click($this->rq()->show($sessionId));

        return $this->response;
    }

    /**
     * @return void
     */
    private function pools()
    {
        $this->cl(Deposit::class)->show($this->session);
        $this->cl(Remitment::class)->show($this->session);
    }

    /**
     * @return void
     */
    private function savings()
    {
        $this->cl(Saving::class)->show($this->session);
        $this->cl(Closing::class)->show($this->session);
    }

    /**
     * @return void
     */
    private function credits()
    {
        $this->cl(Loan::class)->show($this->session);
        $this->cl(PartialRefund::class)->show($this->session);
        $this->cl(Refund::class)->show($this->session);
    }

    /**
     * @return void
     */
    private function cash()
    {
        $this->cl(Disbursement::class)->show($this->session);
    }

    /**
     * @return void
     */
    private function charges()
    {
        $this->cl(FixedFee::class)->show($this->session);
        $this->cl(LibreFee::class)->show($this->session);
    }

    /**
     * @return void
     */
    private function reports()
    {
        // Summernote options
        $options = [
            'height' => 300,
            'toolbar' => [
                // [groupName, [list of button]],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                // ['height', ['height']],
            ],
        ];
        $this->jq('#session-agenda')->summernote($options);
        $this->jq('#session-report')->summernote($options);
        $agendaText = jq('#session-agenda')->summernote('code');
        $reportText = jq('#session-report')->summernote('code');
        $this->jq('#btn-save-agenda')->click($this->rq()->saveAgenda($agendaText));
        $this->jq('#btn-save-report')->click($this->rq()->saveReport($reportText));
    }

    /**
     * @databag refund
     * @before getSessionFromArgs
     * @di $tontineService
     */
    public function show(int $sessionId)
    {
        $html = $this->render('pages.meeting.session.home', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);
        $this->jq('a', '#session-tabs')->click(jq()->tab('show'));
        $tontineId = jq()->parent()->attr('data-tontine-id')->toInt();
        $this->jq('.btn-tontine-edit')->click($this->rq()->edit($tontineId));

        $openQuestion = trans('tontine.session.questions.open') . '<br/>' .
            trans('tontine.session.questions.warning');
        $this->jq('#btn-session-back')->click($this->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->show($sessionId));
        $this->jq('#btn-session-open')->click($this->rq()->open()->confirm($openQuestion));
        $this->jq('#btn-session-close')->click($this->rq()->close()
            ->confirm(trans('tontine.session.questions.close')));

        $this->reports();
        $this->pools();
        $this->charges();
        $this->savings();
        $this->credits();
        $this->cash();

        return $this->response;
    }

    /**
     * @databag refund
     * @before getSession
     * @di $tontineService
     */
    public function open()
    {
        $this->sessionService->openSession($this->session);
        $this->show($this->session->id);

        return $this->response;
    }

    /**
     * @databag refund
     * @before getSession
     * @di $tontineService
     */
    public function close()
    {
        $this->sessionService->closeSession($this->session);
        $this->show($this->session->id);

        return $this->response;
    }

    /**
     * @before getSession
     */
    public function saveAgenda(string $text)
    {
        $this->sessionService->saveAgenda($this->session, $text);
        $this->notify->success(trans('meeting.messages.agenda.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /**
     * @before getSession
     */
    public function saveReport(string $text)
    {
        $this->sessionService->saveReport($this->session, $text);
        $this->notify->success(trans('meeting.messages.report.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
