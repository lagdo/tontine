<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\OpenedSessionCallable;
use App\Ajax\Web\Meeting\Session;
use App\Ajax\Web\Tontine\Options;

use function Jaxon\jq;

/**
 * @databag meeting
 */
class Menu extends OpenedSessionCallable
{
    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        return (int)$this->target()->args()[0];
    }

    /**
     * @return void
     */
    private function showPage(string $name)
    {
        $this->bag('meeting')->set('session.id', $this->session->id);

        $this->view()->share('currentSessionPage', $name);
        $html = $this->renderView("pages.meeting.session.home.$name", [
            'session' => $this->session,
        ]);
        $this->response->html('content-home', $html);

        $this->jq('#btn-session-back')->click($this->rq(Session::class)->home());
        $this->jq('#btn-tontine-options')->click($this->rq(Options::class)->editOptions());
        $this->jq('#btn-session-refresh')->click($this->rq()->$name($this->session->id));

        $this->jq('.btn-session-pools')->click($this->rq()->pools($this->session->id));
        $this->jq('.btn-session-savings')->click($this->rq()->savings($this->session->id));
        $this->jq('.btn-session-credits')->click($this->rq()->credits($this->session->id));
        $this->jq('.btn-session-cash')->click($this->rq()->cash($this->session->id));
        $this->jq('.btn-session-charges')->click($this->rq()->charges($this->session->id));
        $this->jq('.btn-session-reports')->click($this->rq()->reports($this->session->id));
    }

    public function pools(int $sessionId)
    {
        $this->showPage('pools');

        $this->cl(Pool\Deposit::class)->show($this->session);
        $this->cl(Pool\Remitment::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-pools-sm-screens');

        return $this->response;
    }

    public function savings(int $sessionId)
    {
        $this->showPage('savings');

        $this->cl(Saving\Saving::class)->show($this->session);
        $this->cl(Saving\Closing::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-savings-sm-screens');

        return $this->response;
    }

    public function credits(int $sessionId)
    {
        $this->showPage('credits');

        $this->cl(Credit\Loan::class)->show($this->session);
        $this->cl(Credit\PartialRefund::class)->show($this->session);
        $this->cl(Credit\Refund::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-credits-sm-screens');

        return $this->response;
    }

    public function cash(int $sessionId)
    {
        $this->showPage('cash');

        $this->cl(Cash\Disbursement::class)->show($this->session);

        return $this->response;
    }

    public function charges(int $sessionId)
    {
        $this->showPage('charges');

        $this->cl(Charge\FixedFee::class)->show($this->session);
        $this->cl(Charge\LibreFee::class)->show($this->session);

        $this->response->call('setSmScreenHandler', 'session-charges-sm-screens');

        return $this->response;
    }

    public function reports(int $sessionId)
    {
        $this->showPage('reports');

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
        $this->jq('#btn-save-agenda')->click($this->rq(Misc::class)->saveAgenda($agendaText));
        $this->jq('#btn-save-report')->click($this->rq(Misc::class)->saveReport($reportText));

        return $this->response;
    }
}
