<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

/**
 * @databag summary.saving
 * @before getFund
 */
class Member extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'summary.saving';

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
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.saving.member.home', [
            'fund' => $this->getStashedFund(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberTotal::class)->render();
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag($this->bagId)->set('member.search', '');
        $this->bag($this->bagId)->set('member.page', 1);

        $this->render();
    }

    public function search(string $search)
    {
        $this->bag($this->bagId)->set('member.search', trim($search));
        $this->bag($this->bagId)->set('member.page', 1);

        $this->cl(MemberPage::class)->page();
    }
}
