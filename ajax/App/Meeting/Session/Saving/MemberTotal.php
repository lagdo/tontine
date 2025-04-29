<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
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
