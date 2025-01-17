<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
class MemberPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.saving', 'member.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private FundService $fundService,
        private SavingService $savingService)
    {}

    protected function getFund()
    {
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag('meeting.saving')->get('member.search', ''));
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.saving.fund');

        return $this->savingService->getMemberCount($session, $fund, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = trim($this->bag('meeting.saving')->get('member.search', ''));
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.saving.fund');

        return $this->renderView('pages.meeting.saving.member.page', [
            'session' => $session,
            'members' => $this->savingService
                ->getMembers($session, $fund, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-session-saving-members');
    }
}
