<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\Base\Round\Component;
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
        return $this->renderView('pages.meeting.summary.saving.member.total', [
            'fund' => $this->getStashedFund(),
        ]);
    }
}
