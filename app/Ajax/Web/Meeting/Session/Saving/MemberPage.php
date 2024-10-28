<?php

namespace App\Ajax\Web\Meeting\Session\Saving;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag meeting.saving
 * @before getFund
 */
class MemberPage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting.saving', 'member.page'];

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private FundService $fundService,
        private SavingService $savingService)
    {}

    protected function getFund()
    {
        $fundId = $this->bag('refund')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        Cache::set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = trim($this->bag('meeting.saving')->get('member.search', ''));
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');

        return (string)$this->renderView('pages.meeting.saving.member.page', [
            'session' => $session,
            'members' => $this->savingService
                ->getMembers($session, $fund, $search, $filter, $this->page),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('meeting.saving')->get('member.search', ''));
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');

        return $this->savingService->getMemberCount($session, $fund, $search, $filter);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-saving-members');

        return $this->response;
    }
}
