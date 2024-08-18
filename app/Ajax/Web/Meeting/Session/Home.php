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
     * @return void
     */
    private function setup()
    {
        $this->jq('#btn-session-back')->click($this->rq(Menu::class)->home());
        $this->jq('#btn-tontine-options')->click($this->rq(Options::class)->editOptions());

        $this->response->call('showBalanceAmountsWithDelay');
    }

    public function pools(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.pools', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->pools($this->session->id));

        $this->cl(Deposit::class)->show($this->session);
        $this->cl(Remitment::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-pools-sm-screens');

        return $this->response;
    }

    public function savings(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.savings', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->savings($this->session->id));

        $this->cl(Saving::class)->show($this->session);
        $this->cl(Closing::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-savings-sm-screens');

        return $this->response;
    }

    public function credits(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.credits', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->credits($this->session->id));

        $this->cl(Loan::class)->show($this->session);
        $this->cl(PartialRefund::class)->show($this->session);
        $this->cl(Refund::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-credits-sm-screens');

        return $this->response;
    }

    public function cash(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.cash', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->cash($this->session->id));

        $this->cl(Disbursement::class)->show($this->session);

        return $this->response;
    }

    public function charges(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.charges', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->charges($this->session->id));

        $this->cl(FixedFee::class)->show($this->session);
        $this->cl(LibreFee::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-charges-sm-screens');

        return $this->response;
    }

    public function reports(int $sessionId)
    {
        $html = $this->renderView('pages.meeting.session.home.reports', [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->setup();
        $this->jq('#btn-session-refresh')->click($this->rq()->reports($this->session->id));

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

        return $this->response;
    }
}
