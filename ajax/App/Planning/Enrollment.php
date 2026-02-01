<?php

namespace Ajax\App\Planning;

use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('planning.member')]
#[Databag('planning.charge')]
class Enrollment extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    #[Before('setSectionTitle', ["planning", "enrollment"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.planning.participation', [
            'guild' => $this->guild(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response()->jo('tontine')->setSmScreenHandler('finance-sm-screens');

        $this->cl(Member\Member::class)->render();
        $this->cl(Charge\Charge::class)->render();
    }
}
