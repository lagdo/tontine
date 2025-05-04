<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Component;
use Siak\Tontine\Service\Planning\SubscriptionService;

class MemberCounter extends Component
{
    /**
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(protected SubscriptionService $subscriptionService)
    {}

    public function html(): string
    {
        $pool = $this->stash()->get('planning.finance.pool');

        return (string)$this->subscriptionService->getSubscriptionCount($pool);
    }
}
