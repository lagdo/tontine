<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

use function jq;

/**
 * @databag meeting
 * @before getSession
 */
class Charge extends CallableClass
{
    /**
     * @di
     * @var MeetingService
     */
    protected MeetingService $meetingService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->meetingService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show($session, $meetingService)
    {
        $this->session = $session;
        $this->meetingService = $meetingService;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('pages.meeting.charge.home')
            ->with('session', $this->session);
        $this->response->html('meeting-charges', $html);
        $this->jq('#btn-charges-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $charges = $this->meetingService->getCharges($this->session, $pageNumber);
        $chargeCount = $this->meetingService->getChargeCount();

        $html = $this->view()->render('pages.meeting.charge.page')
            ->with('charges', $charges)->with('session', $this->session)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $chargeCount));
        $this->response->html('meeting-charges-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id');
        $this->jq('.btn-charge-settlements')->click($this->cl(Settlement::class)->rq()->home($chargeId));
        $this->jq('.btn-charge-fine')->click($this->cl(Fine::class)->rq()->home($chargeId));

        return $this->response;
    }
}
