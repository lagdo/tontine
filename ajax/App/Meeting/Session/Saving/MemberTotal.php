<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Stringable;

#[Exclude]
class MemberTotal extends Component
{
    use FundTrait;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.saving.member.total', [
            'fund' => $this->getStashedFund(),
        ]);
    }
}
