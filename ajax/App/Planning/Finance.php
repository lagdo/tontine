<?php

namespace Ajax\App\Planning;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Ajax\Page\SectionContent;
use Stringable;

#[Databag('planning.fund')]
#[Databag('planning.pool')]
#[Export(base: ['render'])]
class Finance extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["planning", "finance"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance', [
            'guild' => $this->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->setSmScreenHandler('finance-sm-screens');

        $this->cl(Fund\Fund::class)->render();
        $this->cl(Pool\Pool::class)->render();
    }
}
