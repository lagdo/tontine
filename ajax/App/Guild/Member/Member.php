<?php

namespace Ajax\App\Guild\Member;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;
use function trim;

/**
 * @databag member
 * @before checkHostAccess ["guild", "members"]
 */
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @after hideMenuOnMobile
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
        $this->bag('member')->set('search', '');
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

    public function search(string $search)
    {
        $this->bag('member')->set('search', trim($search));
        $this->bag('member')->set('page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
