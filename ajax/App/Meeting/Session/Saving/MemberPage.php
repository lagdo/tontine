<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

#[Before('getFund')]
#[Databag('meeting.saving')]
class MemberPage extends PageComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.saving';

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.saving', 'member.page'];

    /**
     * The constructor
     *
     * @param SavingService $savingService
     */
    public function __construct(protected SavingService $savingService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = $this->bag($this->bagId)->get('member.search', '');
        $filter = $this->bag($this->bagId)->get('member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->savingService->getMemberCount($session, $fund, $search, $filter);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag($this->bagId)->get('member.search', '');
        $filter = $this->bag($this->bagId)->get('member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

        return $this->renderView('pages.meeting.session.saving.member.page', [
            'session' => $session,
            'members' => $this->savingService
                ->getMembers($session, $fund, $search, $filter, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-session-saving-members');
    }
}
