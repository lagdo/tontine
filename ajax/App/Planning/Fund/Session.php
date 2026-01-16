<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;

#[Before('getFund')]
#[Databag('planning.fund')]
#[Export(base: ['render'])]
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

    public function html(): string
    {
        return $this->renderTpl('pages.planning.fund.session.home', [
            'fund' => $this->stash()->get('planning.fund'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(SessionHeader::class)->render();
        $this->cl(SessionPage::class)->end();
    }
}
