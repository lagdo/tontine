<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Cache;
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
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');

        return (string)$this->renderView('pages.meeting.charge.libre.member.page', [
            'session' => $session,
            'charge' => $charge,
            'members' => $this->billService
                ->getMembers($charge, $session, $search, $filter, $this->page),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');

        return $this->billService->getMemberCount($charge, $session, $search, $filter);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-fee-libre-members');

        return $this->response;
    }
}
