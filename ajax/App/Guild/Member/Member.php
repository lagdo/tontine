<?php

namespace Ajax\App\Guild\Member;

use Ajax\Base\Guild\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

use function trim;

#[Before('checkHostAccess', ["guild", "members"])]
#[Databag('guild.member')]
#[Export(base: ['render'])]
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["guild", "members"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.member.home');
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
        $filter = $this->bag('guild.member')->get('filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('guild.member')->set('filter', $filter);
        $this->bag('guild.member')->set('page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('guild.member')->set('search', trim($search));
        $this->bag('guild.member')->set('page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
