<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use App\Events\OnPagePaymentHome;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag payment
 * @before checkGuestAccess ["meeting", "payments"]
 */
class Payment extends CallableClass
{
    /**
     * @var Collection
     */
    private Collection $sessions;

    /**
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    protected function getOpenedSessions()
    {
        $this->sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    /**
     * @before getOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->renderView('pages.meeting.payment.home', [
            'sessions' => $this->sessions,
        ]);
        $this->response->html('content-home', $html);

        OnPagePaymentHome::dispatch();

        return $this->cl(PaymentPage::class)->page();
    }
}
