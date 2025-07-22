<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\PageComponent;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @databag meeting.payment
 * @before checkHostAccess ["meeting", "payments"]
 */
class PaymentPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.payment', 'page'];

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService)
    {}

    private function getOpenedSessions(): Collection
    {
        $round = $this->stash()->get('tenant.round');
        return $this->sessionService->getSessions($round, orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        return $this->memberService->getMemberCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.meeting.payment.page', [
            'sessions' => $this->getOpenedSessions(),
            'members' => $this->memberService->getMembers($round, page: $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-payment-page');
    }
}
