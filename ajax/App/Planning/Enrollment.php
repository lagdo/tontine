<?php

namespace Ajax\App\Planning;

use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Stringable;

use function trans;

/**
 * @databag planning.member
 * @databag planning.charge
 */
class Enrollment extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     */
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
        return $this->renderView('pages.planning.participation', [
            'guild' => $this->tenantService->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->setSmScreenHandler('finance-sm-screens');

        $this->cl(Member\Member::class)->render();
        $this->cl(Charge\Charge::class)->render();
    }
}
