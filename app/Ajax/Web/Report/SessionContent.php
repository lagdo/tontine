<?php

namespace App\Ajax\Web\Report;

use App\Ajax\Component;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;

/**
 * @exclude
 * @databag report
 */
class SessionContent extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.report.session.session');
    }

    public function show(SessionModel $session, MemberModel $member = null)
    {
        $this->bag('report')->set('session.id', $session->id);

        $this->cl(Session\ReportTitle::class)->init($session, $member)->render();

        // Initialize the page components.
        $this->cl(Session\Bill\Session::class)->init($session, $member);
        $this->cl(Session\Bill\Total::class)->init($session, $member);
        $this->cl(Session\Deposit::class)->init($session, $member);
        $this->cl(Session\Disbursement::class)->init($session, $member);
        $this->cl(Session\Loan::class)->init($session, $member);
        $this->cl(Session\Refund::class)->init($session, $member);
        $this->cl(Session\Remitment::class)->init($session, $member);
        $this->cl(Session\Saving::class)->init($session, $member);
        $this->cl(Session\Saving\Fund::class)->clear();

        $this->render();

        // Render the page buttons.
        $this->cl(Session\Action\Export::class)->setSessionId($session->id)->render();
        $this->cl(Session\Action\Menu::class)->setSessionId($session->id)->render();
    }
}
