<?php

namespace App\Ajax\Web\Planning\Subscription;

use Jaxon\App\Component;
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
        $pool = $this->cl(Home::class)->getPool();

        return (string)$this->subscriptionService->getSubscriptionCount($pool);
    }
}
