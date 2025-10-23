<?php

namespace Ajax\App\Planning;

use Ajax\App\Planning\Component;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

#[Databag('planning.fund')]
#[Databag('planning.pool')]
class Finance extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    #[Callback('jaxon.ajax.callback.hideMenuOnMobile')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->setSmScreenHandler('finance-sm-screens');

        $this->cl(Fund\Fund::class)->render();
        $this->cl(Pool\Pool::class)->render();
    }
}
