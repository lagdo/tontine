<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Stringable;

#[Exclude]
class MemberTotal extends Component
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
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        [$count, $amount] = $this->billService->getBillTotal($charge, $session);

        return $this->renderView('pages.meeting.session.charge.libre.member.total', [
            'billCount' => $count,
            'billAmount' => $amount,
            'memberCount' => $this->billService->getMemberCount($charge, $session),
        ]);
    }
}
