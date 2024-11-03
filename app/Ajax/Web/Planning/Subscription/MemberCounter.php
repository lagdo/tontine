<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
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
        $pool = $this->cache->get('subscription.pool');

        return (string)$this->subscriptionService->getSubscriptionCount($pool);
    }
}
