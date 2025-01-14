<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use App\Events\OnPagePaymentHome;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

use function trans;

/**
 * @databag payment
 * @before checkHostAccess ["meeting", "payments"]
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
    public function home()
    {
        $this->render();
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
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.payment.home', [
            'sessions' => $this->sessions,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PaymentPage::class)->page();

        OnPagePaymentHome::dispatch($this->sessions);
    }
}
