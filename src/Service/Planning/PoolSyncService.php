<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;

use function count;

class PoolSyncService
{
    use SessionSyncTrait;

    /**
     * @param Round $round
     * @param Collection|array $sessions
     *
     * @return void
     */
    public function sessionsCreated(Round $round, Collection|array $sessions): void
    {
        // Create the receivables.
        $receivables = [];
        $poolFilter = fn($query, $session) => $query->ofRound($round)
            ->whereHas('start', fn($qs) => $qs->where('day_date', '<', $session->day_date))
            ->whereHas('end', fn($qe) => $qe->where('day_date', '>', $session->day_date));
        foreach($sessions as $session)
        {
            // Take all the subscriptions to a pool this session belongs to.
            $subscriptions = Subscription::whereNotNull('id')
                ->whereHas('pool', fn($query) =>
                    $poolFilter($query, $session));
            foreach($subscriptions->get() as $subscription)
            {
                $receivables[] = [
                    'subscription_id' => $subscription->id,
                    'session_id' => $session->id,
                ];
            }
        }
        if(count($receivables) > 0)
        {
            DB::table('receivables')->insert($receivables);
        }

        // Update the start and end sessions.
        $round->pools()->update([
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Session $session
     *
     * @return void
     */
    public function sessionDeleted(Session $session)
    {
        // Detach the payables and delete the receivables.
        $session->payables()->update(['session_id' => null]);
        $session->receivables()->delete();
        $session->disabled_pools()->detach();

        $round = $session->round;
        if($round->sessions()->count() === 1)
        {
            // The last session is being deleted.
            $round->pools()->delete();
            return;
        }

        // Update the start sessions.
        $nextSession = $this->getNextSession($round, $session);
        if($nextSession !== null)
        {
            $round->pools()
                ->where('start_sid', $session->id)
                ->update(['start_sid' => $nextSession->id]);
        }

        // Update the end sessions.
        $prevSession = $this->getPrevSession($round, $session);
        if($prevSession !== null)
        {
            $round->pools()
                ->where('end_sid', $session->id)
                ->update(['end_sid' => $prevSession->id]);
        }
    }

    /**
     * @param Pool $pool
     *
     * @return void
     */
    public function sessionsChanged(Pool $pool): void
    {
        // The relations need to be reloaded.
        $pool->refresh();

        // Delete the receivables for the sessions removed from the pool.
        Receivable::whereHas('subscription', fn($qs) =>
                $qs->where('pool_id', $pool->id))
            ->whereHas('session', fn($qw) =>
                $qw->where(fn($qs) => $qs
                    ->orWhere('day_date', '<', $pool->start->day_date)
                    ->orWhere('day_date', '>', $pool->end->day_date)))
            ->delete();

        // Create the receivables for the sessions added to the pool.
        $receivables = [];
        foreach($pool->sessions as $session)
        {
            $subscriptions = $pool->subscriptions()
                ->whereDoesntHave('receivables', fn($qr) =>
                    $qr->where('session_id', $session->id));
            foreach($subscriptions->get() as $subscription)
            {
                $receivables[] = [
                    'subscription_id' => $subscription->id,
                    'session_id' => $session->id,
                ];
            }
        }
        if(count($receivables) > 0)
        {
            DB::table('receivables')->insert($receivables);
        }
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function sessionEnabled(Pool $pool, Session $session): void
    {
        // Create the receivables for the pool subscriptions.
        $receivables = [];
        foreach($pool->subscriptions as $subscription)
        {
            $receivables[] = [
                'subscription_id' => $subscription->id,
                'session_id' => $session->id,
            ];
        }
        if(count($receivables) > 0)
        {
            DB::table('receivables')->insert($receivables);
        }
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function sessionDisabled(Pool $pool, Session $session): void
    {
        // Detach the payables.
        $session->payables()
            ->whereHas('subscription', fn($qs) =>
                $qs->where('pool_id', $pool->id))
            ->update(['session_id' => null]);

        // Delete the receivables
        $session->receivables()
            ->whereHas('subscription', fn($qs) =>
                $qs->where('pool_id', $pool->id))
            ->delete();
    }

    /**
     * @param Subscription $subscription
     *
     * @return void
     */
    public function subscriptionCreated(Subscription $subscription)
    {
        // Create the payable
        $subscription->payable()->create([]);

        // Create the receivables for the pool sessions.
        $receivables = [];
        foreach($subscription->pool->sessions as $session)
        {
            $receivables[] = [
                'subscription_id' => $subscription->id,
                'session_id' => $session->id,
            ];
        }
        if(count($receivables) > 0)
        {
            DB::table('receivables')->insert($receivables);
        }
    }

    /**
     * @param Subscription $subscription
     *
     * @return void
     */
    public function subscriptionDeleted(Subscription $subscription)
    {
        // Delete the payable
        $subscription->payable()->delete();

        // Delete the receivables
        $subscription->receivables()->delete();
    }
}
