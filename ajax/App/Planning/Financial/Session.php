<?php

namespace Ajax\App\Planning\Financial;

use Ajax\App\Page\SectionContent;
use Ajax\Component;
use Stringable;

/**
 * @databag planning.financial
 * @before getPool
 */
class Session extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    public function pool(int $poolId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.financial.session.home', [
            'pool' => $this->stash()->get('planning.financial.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionAction::class)->render();
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->end();
    }
}
