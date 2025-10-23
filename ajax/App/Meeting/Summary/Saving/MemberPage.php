<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

#[Before('getFund')]
#[Databag('summary.saving')]
class MemberPage extends PageComponent
{
    use FundTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['summary.saving', 'member.page'];

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
        $search = $this->bag('summary.saving')->get('member.search', '');
        $session = $this->stash()->get('summary.session');
        $fund = $this->getStashedFund();

        return $this->savingService->getMemberCount($session, $fund, $search, true);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $search = $this->bag('summary.saving')->get('member.search', '');
        $session = $this->stash()->get('summary.session');
        $fund = $this->getStashedFund();

        return $this->renderView('pages.meeting.summary.saving.member.page', [
            'session' => $session,
            'members' => $this->savingService->getMembers($session,
                $fund, $search, true, $this->currentPage()),
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
