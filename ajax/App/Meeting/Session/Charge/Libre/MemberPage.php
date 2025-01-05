<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\ChargePageComponent;
use Stringable;

use function trim;

class MemberPage extends ChargePageComponent
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
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->billService->getMemberCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');

        return $this->renderView('pages.meeting.charge.libre.member.page', [
            'session' => $session,
            'charge' => $charge,
            'members' => $this->billService
                ->getMembers($charge, $session, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('meeting-fee-libre-members');
    }
}
