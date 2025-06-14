<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\PageComponent;
use Stringable;

class MemberPage extends PageComponent
{
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
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->billService->getMemberCount($round, $charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag('meeting')->get('fee.member.search', '');
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $round = $this->stash()->get('tenant.round');
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.session.charge.libre.member.page', [
            'session' => $session,
            'charge' => $charge,
            'members' => $this->billService->getMembers($round, $charge,
                $session, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-fee-libre-members');
    }
}
