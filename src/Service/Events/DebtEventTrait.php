<?php

namespace Siak\Tontine\Service\Events;

use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;

trait DebtEventTrait
{
    /**
     * @param Session $session
     *
     * @return void
     */
    protected function sessionDeleted(Session $session)
    {
        // Detach from the payables. Don't delete.
        $session->payables()->update(['session_id' => null]);
        // Delete the receivables
        $session->receivables()->delete();
    }

    /**
     * @param Pool $pool
     * @param Subscription $subscription
     *
     * @return void
     */
    protected function subscriptionCreated(Pool $pool, Subscription $subscription)
    {
        // Create the payable
        $subscription->payable()->create([]);
        // Create the receivables
        foreach($pool->sessions()->get() as $session)
        {
            $subscription->receivables()->create(['session_id' => $session->id]);
        }
    }

    /**
     * @param Subscription $subscription
     *
     * @return void
     */
    protected function subscriptionDeleted(Subscription $subscription)
    {
        // Delete the payable
        $subscription->payable()->delete();
        // Delete the receivables
        $subscription->receivables()->delete();
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    protected function poolAttached(Pool $pool, Session $session)
    {
        // Create the receivables
        foreach($pool->subscriptions as $subscription)
        {
            $subscription->receivables()->create(['session_id' => $session->id]);
        }
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    protected function poolDetached(Pool $pool, Session $session)
    {
        // Delete the receivables
        foreach($pool->subscriptions as $subscription)
        {
            $subscription->receivables()->where('session_id', $session->id)->delete();
        }
    }
}
