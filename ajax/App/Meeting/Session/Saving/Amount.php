<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\Component;
use Siak\Tontine\Service\LocaleService;
use Stringable;

/**
 * @exclude
 */
class Amount extends Component
{
    /**
     * The constructor
     *
     * @param LocaleService $localeService
     */
    public function __construct(private LocaleService $localeService)
    {}

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
            return $this->renderView('pages.meeting.session.saving.member.closed', [
                'amount' => !$saving ? '' :
                    $this->localeService->formatMoney($saving->amount),
            ]);
        }

        // When editing the saving amount, or when there is no saving yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.saving.edit');
        if($edit || !$saving)
        {
            return $this->renderView('pages.meeting.session.saving.member.edit', [
                'memberId' => $member->id,
                'amount' => !$saving ? '' :
                    $this->localeService->getMoneyValue($saving->amount),
                'rqAmountFunc' => $this->rq(AmountFunc::class),
            ]);
        }

        return $this->renderView('pages.meeting.session.saving.member.show', [
            'memberId' => $member->id,
            'amount' => $this->localeService->formatMoney($saving->amount, false),
            'rqAmountFunc' => $this->rq(AmountFunc::class),
        ]);
    }
}
