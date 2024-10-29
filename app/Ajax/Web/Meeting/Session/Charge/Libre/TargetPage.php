<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        $this->cache->set('meeting.session.charge.target', $target);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));

        return $this->targetService->getMemberCount($search);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $target = $this->cache->get('meeting.session.charge.target');

        return (string)$this->renderView('pages.meeting.charge.libre.target.page', [
            'session' => $session,
            'target' => $target,
            'charge' => $charge,
            'members' => $this->targetService
                ->getMembersWithSettlements($charge, $target, $search, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fee-libre-target');
    }
}
