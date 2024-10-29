<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Web\Meeting\Session\Charge\ChargePageComponent;

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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return $this->billService->getMemberCount($charge, $session, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');

        return (string)$this->renderView('pages.meeting.charge.libre.member.page', [
            'session' => $session,
            'charge' => $charge,
            'members' => $this->billService
                ->getMembers($charge, $session, $search, $filter, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fee-libre-members');
    }
}
