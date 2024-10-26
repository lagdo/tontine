<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\Session\Charge\ChargePageComponent;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;

use function trim;

/**
 * @before getTarget
 */
class TargetPage extends ChargePageComponent
{
    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected SettlementTargetService $targetService)
    {}

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.target.page'];

    protected function getTarget()
    {
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        Cache::set('meeting.session.charge.target', $target);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $session = Cache::get('meeting.session');
        $charge = Cache::get('meeting.session.charge');
        $target = Cache::get('meeting.session.charge.target');

        return (string)$this->renderView('pages.meeting.charge.libre.target.page', [
            'session' => $session,
            'target' => $target,
            'charge' => $charge,
            'members' => $this->targetService
                ->getMembersWithSettlements($charge, $target, $search, $this->page),
        ]);
    }

    protected function count(): int
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));

        return $this->targetService->getMemberCount($search);
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-fee-libre-target');

        return $this->response;
    }
}
