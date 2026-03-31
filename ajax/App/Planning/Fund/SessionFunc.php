<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Validation\Planning\FundSessionsValidator;

#[Before('getFund')]
#[Databag('planning.fund')]
class SessionFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var FundSessionsValidator
     */
    protected FundSessionsValidator $validator;

    #[Inject(attr: 'validator')]
    public function save(array $formValues): void
    {
        $fund = $this->stash()->get('planning.fund');
        $values = $this->validator->validateItem($formValues);
        $this->fundService->saveSessions($fund, $values);

        // Reload the fund to update the start/end sessions.
        $fund = $this->fundService->getFund($this->round(), $fund->id);
        $this->stash()->set('planning.fund', $fund);

        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->page();
        $this->alert()->success(trans('tontine.session.messages.fund.saved'));
    }
}
