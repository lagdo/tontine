<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Databag;

use function trim;

#[Databag('planning.member')]
class Member extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.member.home', [
            'guild' => $this->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(MemberPage::class)->page();
    }

    public function toggleFilter()
    {
        // Toggle the filter
        $filter = $this->bag('planning.member')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('planning.member')->set('filter', $filter);
        $this->bag('planning.member')->set('page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('planning.member')->set('search', trim($search));
        $this->bag('planning.member')->set('page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
