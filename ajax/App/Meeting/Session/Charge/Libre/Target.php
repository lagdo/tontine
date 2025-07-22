<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\Component;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Stringable;

/**
 * @before getTarget
 */
class Target extends Component
{
    /**
     * @var string
     */
    protected $overrides = Fee::class;

    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected SettlementTargetService $targetService)
    {}

    protected function getTarget(): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        $this->stash()->set('meeting.session.charge.target', $target);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.charge.libre.target.home', [
            'charge' => $this->stash()->get('meeting.session.charge'),
            'target' => $this->stash()->get('meeting.session.charge.target'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        // The member list is displayed only if a target is already defined.
        if($this->stash()->get('meeting.session.charge.target') !== null)
        {
            $this->cl(TargetPage::class)->page();
        }
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId): void
    {
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.target.page', 1);

        $this->render();
    }

    public function search(string $search): void
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));
        $this->bag('meeting')->set('fee.target.page', 1);

        $this->cl(TargetPage::class)->page();
    }
}
