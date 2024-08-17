<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Session as Menu;
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
use App\Ajax\Web\Tontine\Options;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function trans;

class Home extends CallableSessionClass
{
    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->target()->args()[0];
        $this->session = $this->sessionService->getSession($sessionId);
        $this->bag('meeting')->set('session.id', $sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home($session->id);
    }

    public function home(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);
        $this->jq('a', '#session-tabs')->click(jq()->tab('show'));
        $tontineId = jq()->parent()->attr('data-tontine-id')->toInt();
        $this->jq('.btn-tontine-edit')->click($this->rq()->edit($tontineId));

        $this->jq('#btn-session-back')->click($this->rq(Menu::class)->home());
        $this->jq('#btn-tontine-options')->click($this->rq(Options::class)->editOptions());
        $this->jq('#btn-session-refresh')->click($this->rq()->home($sessionId));
        $this->jq('#btn-session-open')->click($this->rq(Session::class)->open()
            ->confirm(trans('tontine.session.questions.open') . '<br/>' .
            trans('tontine.session.questions.warning')));
        $this->jq('#btn-session-close')->click($this->rq(Session::class)->close()
            ->confirm(trans('tontine.session.questions.close')));

        $this->reports();
        $this->pools();
        $this->charges();
        $this->savings();
        $this->credits();
        $this->cash();

        $this->response->call('showBalanceAmountsWithDelay');

        return $this->response;
    }

    /**
     * @return void
     */
    private function pools()
    {
        $this->cl(Deposit::class)->show($this->session);
        $this->cl(Remitment::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-pools-sm-screens', 'session-pools');
    }

    /**
     * @return void
     */
    private function savings()
    {
        $this->cl(Saving::class)->show($this->session);
        $this->cl(Closing::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-savings-sm-screens', 'session-savings');
    }

    /**
     * @return void
     */
    private function credits()
    {
        $this->cl(Loan::class)->show($this->session);
        $this->cl(PartialRefund::class)->show($this->session);
        $this->cl(Refund::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-credits-sm-screens', 'session-credits');
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

        $this->response->call('setSmScreenHandler', 'session-charges-sm-screens', 'session-charges');
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
        $this->jq('#btn-save-agenda')->click($this->rq(Session::class)->saveAgenda($agendaText));
        $this->jq('#btn-save-report')->click($this->rq(Session::class)->saveReport($reportText));
    }
}
