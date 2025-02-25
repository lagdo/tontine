<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\PageComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
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
    public function __construct(private SavingService $savingService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag($this->bagId)->get('member.search', ''));
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
        $search = trim($this->bag($this->bagId)->get('member.search', ''));
        $filter = $this->bag($this->bagId)->get('member.filter', null);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();

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
