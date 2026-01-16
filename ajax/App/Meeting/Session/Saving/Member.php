<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Saving\SavingService;

#[Before('getFund')]
#[Databag('meeting.saving')]
class Member extends Component
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
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.saving.member.home', [
            'fund' => $this->getStashedFund(),
            'session' => $this->stash()->get('meeting.session'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(MemberTotal::class)->render();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId): void
    {
        $this->bag($this->bagId)->set('member.filter', null);
        $this->bag($this->bagId)->set('member.search', '');
        $this->bag($this->bagId)->set('member.page', 1);

        $this->render();
    }

    public function search(string $search): void
    {
        $this->bag($this->bagId)->set('member.search', trim($search));
        $this->bag($this->bagId)->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }

    public function toggleFilter(): void
    {
        $filter = $this->bag($this->bagId)->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag($this->bagId)->set('member.filter', $filter);
        $this->bag($this->bagId)->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
