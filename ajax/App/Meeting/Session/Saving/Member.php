<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
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
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.member.home', [
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
        $this->bag($this->bagId)->set('member.filter', null);
        $this->bag($this->bagId)->set('member.search', '');
        $this->bag($this->bagId)->set('member.page', 1);

        $this->render();
    }
}
