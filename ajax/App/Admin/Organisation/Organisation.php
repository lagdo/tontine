<?php

namespace Ajax\App\Admin\Organisation;

use Ajax\Component;
use Ajax\App\Admin\User\Guest\Organisation as GuestOrganisation;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Siak\Tontine\Service\Tontine\TontineService;
use Stringable;

use function trans;

/**
 * @databag tontine
 */
class Organisation extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param TontineService $tontineService
     */
    public function __construct(private TontineService $tontineService)
    {}

    /**
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontines'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.admin.organisation.home', [
            'hasGuestOrganisations' => $this->tontineService->hasGuestOrganisations(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(OrganisationPage::class)->page();
        if($this->tontineService->hasGuestOrganisations())
        {
            $this->cl(GuestOrganisation::class)->render();
        }
    }
}
