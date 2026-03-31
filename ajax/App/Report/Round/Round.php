<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Siak\Tontine\Service\Meeting\Session\SummaryService;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
#[Before('getPools')]
class Round extends Component
{
    use Table\PoolTrait;

    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    /**
     * @param SummaryService $summaryService
     */
    public function __construct(private SummaryService $summaryService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.report.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(RoundFunc::class)->showTables();
    }

    /**
     * @return void
     */
    #[Before('setSectionTitle', ["report", "round"])]
    #[Callback('tontine.hideMenu')]
    public function home(): void
    {
        $this->render();
    }
}
