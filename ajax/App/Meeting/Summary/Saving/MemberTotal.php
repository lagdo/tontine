<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class MemberTotal extends Component
{
    use FundTrait;

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.summary.saving.member.total', [
            'fund' => $this->getStashedFund(),
        ]);
    }
}
