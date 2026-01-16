<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Saving\SavingService;

use function trim;

#[Before('getFund')]
#[Databag('summary.saving')]
class Member extends Component
{
    use FundTrait;

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
        return $this->renderTpl('pages.meeting.summary.saving.member.home', [
            'fund' => $this->getStashedFund(),
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
     * @return void
     */
    public function fund(int $fundId): void
    {
        $this->bag('summary.saving')->set('member.search', '');
        $this->bag('summary.saving')->set('member.page', 1);

        $this->render();
    }

    /**
     * @param string $search
     *
     * @return void
     */
    public function search(string $search): void
    {
        $this->bag('summary.saving')->set('member.search', trim($search));
        $this->bag('summary.saving')->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
