<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Tontine\FundService;

use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class MemberFunc extends FuncComponent
{
    /**
     * @var string
     */
    protected $overrides = Saving::class;

    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(private FundService $fundService)
    {}

    protected function getFund()
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    public function search(string $search)
    {
        $this->bag('meeting.saving')->set('member.search', trim($search));
        $this->bag('meeting.saving')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting.saving')->set('member.filter', $filter);

        $this->cl(MemberPage::class)->page();
    }
}
