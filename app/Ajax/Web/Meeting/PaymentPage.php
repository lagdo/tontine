<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\PageComponent;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

/**
 * @databag payment
 * @before checkGuestAccess ["meeting", "payments"]
 */
class PaymentPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['payment', 'page'];

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService)
    {}

    private function getOpenedSessions(): Collection
    {
        return $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->memberService->getMemberCount('');
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.payment.page', [
            'sessions' => $this->getOpenedSessions(),
            'members' => $this->memberService->getMembers('', $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('payment-members-page');
    }
}
