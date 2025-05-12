<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Stringable;

/**
 * @databag planning.fund
 * @before getFund
 */
class Session extends Component
{
    use FundTrait;

    /**
     * @var string
     */
    protected $overrides = Fund::class;

    public function fund(int $fundId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.fund.session.home', [
            'fund' => $this->stash()->get('planning.fund'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->end();
    }
}
