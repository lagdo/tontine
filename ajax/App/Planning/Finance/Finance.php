<?php

namespace Ajax\App\Planning\Finance;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag planning.finance.fund
 * @databag planning.finance.pool
 */
class Finance extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkHostAccess ["planning", "finance"]
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.finance.home', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->setSmScreenHandler('finance-sm-screens');

        $this->cl(Fund\Fund::class)->render();
        $this->cl(Pool\Pool::class)->render();
    }
}
