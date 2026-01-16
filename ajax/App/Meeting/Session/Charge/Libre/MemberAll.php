<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;

#[Exclude]
class MemberAll extends Component
{
    /**
     * The constructor
     *
     * @param BillService $billService
     */
    public function __construct(private BillService $billService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $search = $this->bag('meeting')->get('fee.member.search', '');

        return $this->renderTpl('pages.meeting.session.charge.libre.member.all', [
            'charge' => $charge,
            'memberCount' => $this->billService->getMemberCount($charge,
                $session, $search),
            'noBillCount' => $this->billService->getMemberCount($charge,
                $session, $search, false),
        ]);
    }
}
