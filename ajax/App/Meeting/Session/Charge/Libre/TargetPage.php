<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;

#[Before('getTarget')]
class TargetPage extends PageComponent
{
    use ChargeTrait;

    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected SettlementTargetService $targetService)
    {}

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.target.page'];

    protected function getTarget()
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $target = $session === null || $charge === null ? null :
            $this->targetService->getTarget($charge, $session);
        $this->stash()->set('meeting.session.charge.target', $target);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('meeting')->get('fee.member.search', '');

        return $this->targetService->getMemberCount($this->round(), $search);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $target = $this->stash()->get('meeting.session.charge.target');

        return $this->renderTpl('pages.meeting.session.charge.libre.target.page', [
            'session' => $session,
            'target' => $target,
            'charge' => $charge,
            'members' => $this->targetService->getMembersWithSettlements($this->round(),
                $charge, $target, $search, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-session-fee-libre-target');
    }
}
