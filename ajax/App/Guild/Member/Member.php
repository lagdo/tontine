<?php

namespace Ajax\App\Guild\Member;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;
use function trim;

/**
 * @databag guild.member
 * @before checkHostAccess ["guild", "members"]
 */
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
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
    protected function after()
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
