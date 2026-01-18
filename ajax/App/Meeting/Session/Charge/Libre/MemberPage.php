<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\PageComponent;

class MemberPage extends PageComponent
{
    use ChargeTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.member.page'];

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->billService->getMemberCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderTpl('pages.meeting.session.charge.libre.member.page', [
            'session' => $session,
            'charge' => $charge,
            'members' => $this->billService->getMembers($charge,
                $session, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->makeTableResponsive('content-session-fee-libre-members');
    }
}
