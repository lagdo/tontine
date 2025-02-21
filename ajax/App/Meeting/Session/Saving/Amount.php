<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Component;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Amount extends Component
{
    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FundService $fundService
     */
    public function __construct(private LocaleService $localeService,
        private FundService $fundService)
    {}

    protected function getFund()
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $member = $this->stash()->get('meeting.saving.member');
        $saving = $this->stash()->get('meeting.saving');

        if($session->closed)
        {
            return $this->renderView('pages.meeting.saving.member.closed', [
                'amount' => !$saving ? '' : $this->localeService->formatMoney($saving->amount, true),
            ]);
        }

        // When editing the saving amount, or when there is no saving yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.saving.edit');
        if($edit || !$saving)
        {
            return $this->renderView('pages.meeting.saving.member.edit', [
                'memberId' => $member->id,
                'amount' => !$saving ? '' : $this->localeService->getMoneyValue($saving->amount),
                'rqAmountFunc' => $this->rq(AmountFunc::class),
            ]);
        }

        return $this->renderView('pages.meeting.saving.member.show', [
            'memberId' => $member->id,
            'amount' => $this->localeService->formatMoney($saving->amount, false),
            'rqAmountFunc' => $this->rq(AmountFunc::class),
        ]);
    }
}
