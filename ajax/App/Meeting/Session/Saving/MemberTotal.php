<?php

namespace Ajax\App\Meeting\Session\Saving;

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
        return $this->renderTpl('pages.meeting.session.saving.member.total', [
            'fund' => $this->getStashedFund(),
        ]);
    }
}
