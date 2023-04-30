<?php

namespace App\Ajax\App\Meeting;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Charge\Fee;
use App\Ajax\App\Meeting\Charge\Fine;
use App\Ajax\App\Meeting\Credit\Funding;
use App\Ajax\App\Meeting\Credit\Loan;
use App\Ajax\App\Meeting\Refund\Interest;
use App\Ajax\App\Meeting\Refund\Principal;
use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Meeting\FundingService;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Service\Meeting\PoolService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
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
     * @var FundingService
     */
    protected FundingService $fundingService;

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

    /**
     * @param bool $isMutual
     *
     * @return void
     */
    private function pools(bool $isMutual)
    {
        $this->cl(Deposit::class)->show($this->session, $this->poolService);
        $remitmentClass = ($isMutual ? Remitment\Mutual::class : Remitment\Financial::class);
        $this->cl($remitmentClass)->show($this->session, $this->poolService);
    }

    /**
     * @return void
     */
    private function credits()
    {
        $this->cl(Funding::class)->show($this->session, $this->fundingService);
        $this->cl(Loan::class)->show($this->session, $this->loanService);
        $this->cl(Principal::class)->show($this->session, $this->refundService);
        $this->cl(Interest::class)->show($this->session, $this->refundService);
    }

    /**
     * @return void
     */
    private function charges()
    {
        $this->cl(Fee::class)->show($this->session, $this->feeService);
        $this->cl(Fine::class)->show($this->session, $this->fineService);
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
                // [groupName, [list of button]]
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
     * @di $fundingService
     * @di $loanService
     * @di $refundService
     * @di $feeService
     * @di $fineService
     */
    public function home(int $sessionId)
    {
        $this->bag('meeting')->set('session.id', $sessionId);
        $this->bag('refund')->set('session.id', $sessionId);

        $tontine = $this->poolService->getTontine();
        $html = $this->view()->render('tontine.pages.meeting.session.home', [
            'tontine' => $tontine,
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);
        $this->jq('a', '#session-tabs')->click(pm()->js('function(){' . jq()->tab('show') . '}'));

        $openQuestion = trans('tontine.session.questions.open') . '<br/>' .
            trans('tontine.session.questions.warning');
        $this->jq('#btn-session-back')->click($this->cl(Meeting::class)->rq()->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->home());
        $this->jq('#btn-session-open')->click($this->rq()->open()->confirm($openQuestion));
        $this->jq('#btn-session-close')->click($this->rq()->close()
            ->confirm(trans('tontine.session.questions.close')));

        $this->pools($tontine->is_mutual);
        $this->credits();
        $this->charges();
        $this->reports();

        return $this->response;
    }

    /**
     * @databag refund
     * @di $fundingService
     * @di $loanService
     * @di $refundService
     * @di $feeService
     * @di $fineService
     * @di $sessionService
     */
    public function open()
    {
        $this->sessionService->openSession($this->session);
        $this->home($this->session->id);

        return $this->response;
    }

    /**
     * @databag refund
     * @di $fundingService
     * @di $loanService
     * @di $refundService
     * @di $feeService
     * @di $fineService
     * @di $sessionService
     */
    public function close()
    {
        $this->sessionService->closeSession($this->session);
        $this->home($this->session->id);

        return $this->response;
    }

    /**
     * @di $sessionService
     */
    public function saveAgenda(string $text)
    {
        $this->sessionService->saveAgenda($this->session, $text);
        $this->notify->success(trans('meeting.messages.agenda.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /**
     * @di $sessionService
     */
    public function saveReport(string $text)
    {
        $this->sessionService->saveReport($this->session, $text);
        $this->notify->success(trans('meeting.messages.report.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
