<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Closing;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;

class ClosingService
{
    /**
     * Save the given session as closing for the round on the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     * @param int $profitAmount
     *
     * @return bool
     */
    public function saveRoundClosing(Session $session, Fund $fund, int $profitAmount): bool
    {
        $closing = Closing::firstOrNew([
            'type' => Closing::TYPE_ROUND,
            'session_id' => $session->id,
            'fund_id' => $fund->id,
        ], []);
        $closing->profit = $profitAmount;
        $closing->save();
        return $closing->id > 0;
    }

    /**
     * Save the given session as closing for the round on the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return bool
     */
    public function saveInterestClosing(Session $session, Fund $fund): bool
    {
        $closing = Closing::firstOrCreate([
            'type' => Closing::TYPE_INTEREST,
            'session_id' => $session->id,
            'fund_id' => $fund->id,
        ], []);
        return $closing->id > 0;
    }

    /**
     * Delete a round closing.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return void
     */
    public function deleteRoundClosing(Session $session, Fund $fund)
    {
        Closing::round()
            ->where('session_id', $session->id)
            ->where('fund_id', $fund->id)
            ->delete();
    }

    /**
     * Set an interest closing.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return void
     */
    public function deleteInterestClosing(Session $session, Fund $fund)
    {
        Closing::interest()
            ->where('session_id', $session->id)
            ->where('fund_id', $fund->id)
            ->delete();
    }

    /**
     * Get the round closings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getClosings(Session $session): Collection
    {
        return Closing::where('session_id', $session->id)->get();
    }

    /**
     * Get the round closings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getRoundClosings(Session $session): Collection
    {
        return Closing::round()->where('session_id', $session->id)->get();
    }

    /**
     * Get the interest closings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getInterestClosings(Session $session): Collection
    {
        return Closing::round()->where('session_id', $session->id)->get();
    }

    /**
     * Check if the given session is closing the round for the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return Closing|null
     */
    public function getRoundClosing(Session $session, Fund $fund): ?Closing
    {
        return Closing::round()
            ->where('session_id', $session->id)
            ->where('fund_id', $fund->id)
            ->first();
    }

    /**
     * Get the profit amount saved on a given session.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return int
     */
    public function getProfitAmount(Session $session, Fund $fund): int
    {
        $closing = $this->getRoundClosing($session, $fund);
        return $closing?->profit ?? 0;
    }

    /**
     * Check if the given session is closing the round for the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return Closing|null
     */
    public function getInterestClosing(Session $session, Fund $fund): ?Closing
    {
        return Closing::interest()
            ->where('session_id', $session->id)
            ->where('fund_id', $fund->id)
            ->first();
    }
}
