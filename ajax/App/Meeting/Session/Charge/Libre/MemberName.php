<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;

#[Exclude]
class MemberName extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.charge.libre.member.name', [
            'member' => $this->stash()->get('meeting.charge.member'),
        ]);
    }
}
