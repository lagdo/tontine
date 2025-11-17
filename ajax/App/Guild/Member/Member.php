<?php

namespace Ajax\App\Guild\Member;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Stringable;

use function trans;
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

    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.member.home');
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
