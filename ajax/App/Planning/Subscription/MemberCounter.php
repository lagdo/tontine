<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
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
        $pool = $this->stash()->get('subscription.pool');

        return (string)$this->subscriptionService->getSubscriptionCount($pool);
    }
}
