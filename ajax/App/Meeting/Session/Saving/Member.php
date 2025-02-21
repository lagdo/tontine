<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = Saving::class;

    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(private FundService $fundService)
    {}

    protected function getFund()
    {
        if($this->target()->method() === 'fund')
        {
            $this->bag('meeting.saving')->set('fund.id', $this->target()->args()[0]);
        }
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.member.home', [
            'fund' => $this->stash()->get('meeting.saving.fund'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('member.filter', null);
        $this->bag('meeting.saving')->set('member.search', '');
        $this->bag('meeting.saving')->set('member.page', 1);

        $this->render();
    }
}
