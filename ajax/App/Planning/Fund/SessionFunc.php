<?php

namespace Ajax\App\Planning\Fund;

use Ajax\FuncComponent;
use Siak\Tontine\Validation\Planning\FundSessionsValidator;

/**
 * @databag planning.finance.fund
 * @before getFund
 */
class SessionFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var FundSessionsValidator
     */
    protected FundSessionsValidator $validator;

    /**
     * @di $validator
     */
    public function save(array $formValues)
    {
        $fund = $this->stash()->get('planning.finance.fund');
        $values = $this->validator->validateItem($formValues);
        $this->fundService->saveSessions($fund, $values);

        // Reload the fund to update the start/end sessions.
        $round = $this->tenantService->round();
        $fund = $this->fundService->getFund($round, $fund->id);
        $this->stash()->set('planning.finance.fund', $fund);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
        $this->alert()->success(trans('tontine.session.messages.fund.saved'));
    }
}
