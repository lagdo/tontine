<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;

use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class MemberFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.saving';

    /**
     * @var string
     */
    protected $overrides = Saving::class;

    public function search(string $search)
    {
        $this->bag($this->bagId)->set('member.search', trim($search));
        $this->bag($this->bagId)->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function toggleFilter()
    {
        $filter = $this->bag($this->bagId)->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag($this->bagId)->set('member.filter', $filter);

        $this->cl(MemberPage::class)->page();
    }
}
