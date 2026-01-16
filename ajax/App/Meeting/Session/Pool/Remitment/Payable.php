<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;

#[Before('getPool')]
class Payable extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.remitment.payable.home', [
            'pool' => $this->stash()->get('meeting.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(Total::class)->render();
        $this->cl(PayablePage::class)->render();
    }
}
