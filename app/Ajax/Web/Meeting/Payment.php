<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use App\Events\OnPagePaymentHome;
use Illuminate\Support\Collection;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag payment
 * @before checkGuestAccess ["meeting", "payments"]
 * @before getOpenedSessions
 * @after hideMenuOnMobile
 */
class Payment extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

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
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.meeting'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.payment.home', [
            'sessions' => $this->sessions,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        OnPagePaymentHome::dispatch();
        $this->cl(PaymentPage::class)->page();
    }
}
