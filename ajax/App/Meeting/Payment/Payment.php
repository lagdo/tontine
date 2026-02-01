<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use App\Events\OnPagePaymentHome;
use Illuminate\Support\Collection;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Session\SessionService;

#[Before('checkHostAccess', ["meeting", "payments"])]
#[Before('getOpenedSessions')]
#[Databag('meeting.payment')]
class Payment extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

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
        $this->sessions = $this->sessionService->getSessions($this->round(), orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    #[Before('getOpenedSessions')]
    #[Before('setSectionTitle', ["meeting", "payments"])]
    #[Callback('tontine.hideMenu')]
    public function home(): void
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.payment.home', [
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
