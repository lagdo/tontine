<?php

namespace Ajax\App\Guild\Member;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag member
 */
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["tontine", "members"]
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
}
