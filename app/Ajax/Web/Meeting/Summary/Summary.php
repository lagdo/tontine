<?php

namespace App\Ajax\Web\Meeting\Summary;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before checkGuestAccess ["meeting", "sessions"]
 */
class Summary extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    public function home(int $sessionId): ComponentResponse
    {
        $this->bag('meeting')->set('session.id', $sessionId);
        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $sessionId);

        $session = $this->sessionService->getSession($sessionId);
        if(!$session)
        {
            $this->notify->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            return $this->response;
        }

        $this->cache->set('summary.session', $session);

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.summary.home', [
            'session' => $this->cache->get('summary.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->pools();
        $this->savings();
        $this->credits();
        $this->cash();
        $this->charges();
    }

    private function pools()
    {
        $this->cl(Pool\Deposit::class)->render();
        $this->cl(Pool\Remitment::class)->render();

        $this->response->js()->setSmScreenHandler('session-pools-sm-screens');

        return $this->response;
    }

    private function savings()
    {
        $this->cl(Saving\Saving::class)->render();
        $this->cl(Saving\Closing::class)->render();

        $this->response->js()->setSmScreenHandler('session-savings-sm-screens');

        return $this->response;
    }

    private function credits()
    {
        $this->cl(Credit\Loan::class)->render();
        $this->cl(Credit\PartialRefund::class)->render();
        $this->cl(Credit\Refund::class)->render();

        $this->response->js()->setSmScreenHandler('session-credits-sm-screens');

        return $this->response;
    }

    private function cash()
    {
        $this->cl(Cash\Disbursement::class)->render();

        return $this->response;
    }

    private function charges()
    {
        $this->cl(Charge\FixedFee::class)->render();
        $this->cl(Charge\LibreFee::class)->render();

        $this->response->js()->setSmScreenHandler('session-charges-sm-screens');

        return $this->response;
    }
}
