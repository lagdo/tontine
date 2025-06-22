<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;

class FundSyncService
{
    use SessionSyncTrait;

    /**
     * @param Round $round
     * @param Pool $pool
     *
     * @return void
     */
    private function savePoolFund(Round $round, Pool $pool): void
    {
        Fund::updateOrCreate([
            'pool_id' => $pool->id,
        ], [
            'def_id' => $round->guild->default_fund->id,
            'round_id' => $round->id,
            'start_sid' => $pool->start_sid,
            'end_sid' => $pool->end_sid,
            'interest_sid' => $pool->end_sid,
        ]);
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    private function updateDefaultFund(Round $round): void
    {
        $fundDef = $round->guild->default_fund;
        if(!$round->add_default_fund)
        {
            $fundDef->funds()->real()->where('round_id', $round->id)->delete();
            return;
        }

        Fund::updateOrCreate([
            'def_id' => $fundDef->id,
            'round_id' => $round->id,
        ], [
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
            'interest_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    private function updateFunds(Round $round): void
    {
        if(!$round->start || !$round->end)
        {
            return;
        }

        // Create the fund to be used to lend the money in the pools.
        $round->pools()
            ->whereHas('def', fn($q) => $q->depositLendable())
            ->get()
            ->each(fn($pool) => $this->savePoolFund($round, $pool));

        // Create the default savings fund.
        $this->updateDefaultFund($round);
    }

    /**
     * @param Round $round
     * @param Collection|array $sessions
     *
     * @return void
     */
    public function sessionCreated(Round $round, Collection|array $sessions): void
    {
        $this->updateFunds($round);

        // Update the start and end sessions.
        $round->funds()->update([
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
            'interest_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    public function sessionUpdated(Round $round): void
    {
        // Not necessary
        // $this->updateFunds($round);
    }

    /**
     * @param Session $session
     *
     * @return void
     */
    public function sessionDeleted(Session $session): void
    {
        $round = $session->round;
        if($round->sessions()->count() === 1)
        {
            // The last session is being deleted.
            $round->funds()->delete();
            return;
        }

        // Update the start sessions.
        $nextSession = $this->getNextSession($round, $session);
        if($nextSession !== null)
        {
            $round->funds()
                ->where('start_sid', $session->id)
                ->update(['start_sid' => $nextSession->id]);
        }

        // Update the end sessions.
        $prevSession = $this->getPrevSession($round, $session);
        if($prevSession !== null)
        {
            $round->funds()
                ->where('end_sid', $session->id)
                ->update(['end_sid' => $prevSession->id]);

            $round->funds()
                ->where('interest_sid', $session->id)
                ->update(['interest_sid' => $prevSession->id]);
        }
    }

    /**
     * @param Round $round
     * @param Pool $pool
     *
     * @return void
     */
    public function poolEnabled(Round $round, Pool $pool): void
    {
        if($pool->deposit_lendable)
        {
            // Create the fund to be used to lend the money in the pool.
            $this->savePoolFund($round, $pool);
        }
    }

    /**
     * @param Round $round
     * @param Pool $pool
     *
     * @return void
     */
    public function poolDisabled(Round $round, Pool $pool): void
    {
        if($pool->deposit_lendable)
        {
            // Delete the fund to be used to lend the money in the pool.
            $pool->fund()->where('round_id', $round->id)->delete();
        }
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    public function roundDeleted(Round $round): void
    {
        // Delete the funds (default and for pools) that was automatically created.
        $round->guild->default_fund
            ->funds()
            ->where('round_id', $round->id)
            ->delete();
    }
}
