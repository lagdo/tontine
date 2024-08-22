<?php

namespace App\Ajax\Web\Meeting\Summary;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\Web\Meeting\Session as Menu;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before checkGuestAccess ["meeting", "sessions"]
 */
class Home extends CallableClass
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    public function home(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            $this->notify->error(trans('tontine.session.errors.not_opened'), trans('common.titles.error'));
            return $this->response;
        }

        $this->bag('meeting')->set('session.id', $session->id);

        $html = $this->renderView('pages.meeting.summary.home', [
            'session' => $session,
        ]);
        $this->response->html('content-home', $html);
        $this->jq('a', '#session-tabs')->click(jq()->tab('show'));

        $this->jq('#btn-session-back')->click($this->rq(Menu::class)->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->summary($session->id));

        $this->pools($session);
        $this->savings($session);
        $this->credits($session);
        $this->cash($session);
        $this->charges($session);

        return $this->response;
    }

    private function pools(SessionModel $session)
    {
        $this->cl(Pool\Deposit::class)->show($session);
        $this->cl(Pool\Remitment::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-pools-sm-screens');

        return $this->response;
    }

    private function savings(SessionModel $session)
    {
        $this->cl(Saving\Saving::class)->show($session);
        $this->cl(Saving\Closing::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-savings-sm-screens');

        return $this->response;
    }

    private function credits(SessionModel $session)
    {
        $this->cl(Credit\Loan::class)->show($session);
        $this->cl(Credit\PartialRefund::class)->show($session);
        $this->cl(Credit\Refund::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-credits-sm-screens');

        return $this->response;
    }

    private function cash(SessionModel $session)
    {
        $this->cl(Cash\Disbursement::class)->show($session);

        return $this->response;
    }

    private function charges(SessionModel $session)
    {
        $this->cl(Charge\FixedFee::class)->show($session);
        $this->cl(Charge\LibreFee::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-charges-sm-screens');

        return $this->response;
    }
}
