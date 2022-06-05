<?php

namespace Siak\Tontine\Service\Events;

use Siak\Tontine\Model\Fund;
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
     * @param Fund $fund
     * @param Subscription $subscription
     *
     * @return void
     */
    protected function subscriptionCreated(Fund $fund, Subscription $subscription)
    {
        // Create the payable
        $subscription->payable()->create([]);
        // Create the receivables
        foreach($fund->sessions()->get() as $session)
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
     * @param Fund $fund
     * @param Session $session
     *
     * @return void
     */
    protected function fundAttached(Fund $fund, Session $session)
    {
        // Create the receivables
        foreach($fund->subscriptions as $subscription)
        {
            $subscription->receivables()->create(['session_id' => $session->id]);
        }
    }

    /**
     * @param Fund $fund
     * @param Session $session
     *
     * @return void
     */
    protected function fundDetached(Fund $fund, Session $session)
    {
        // Delete the receivables
        foreach($fund->subscriptions as $subscription)
        {
            $subscription->receivables()->where('session_id', $session->id)->delete();
        }
    }
}
