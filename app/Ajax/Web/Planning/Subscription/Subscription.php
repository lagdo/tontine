<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\Component\SectionContent;
use App\Ajax\Web\Component\SectionTitle;
use Jaxon\Response\AjaxResponse;

use function trans;

/**
 * @databag planning
 * @databag subscription
 */
class Subscription extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @before checkGuestAccess ["planning", "pools"]
     * @after hideMenuOnMobile
     */
    public function home(): AjaxResponse
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(Pool::class)->render();
        $this->response->js()->setSmScreenHandler('pool-subscription-sm-screens');
    }
}
