<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use App\Events\OnPagePaymentHome;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

use function trans;

/**
 * @databag meeting.payment
 * @before checkHostAccess ["meeting", "payments"]
 * @before getOpenedSessions
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
        $round = $this->stash()->get('tenant.round');
        $this->sessions = $this->sessionService->getSessions($round, orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    /**
     * @before getOpenedSessions
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     */
    public function home(): void
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
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
    protected function after(): void
    {
        $this->cl(PaymentPage::class)->page();

        OnPagePaymentHome::dispatch($this->sessions);
    }
}
