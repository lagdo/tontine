<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class MemberEdit extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.charge.libre.member.edit', [
            'id' => $this->cache->get('meeting.session.charge.member'),
            'amount' => $this->cache->get('meeting.session.charge.amount'),
            'hasBill' => true,
        ]);
    }
}
