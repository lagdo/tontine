<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Closing;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;

class ClosingService
{
    /**
     * Save the given session as closing for the savings on the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     * @param int $profitAmount
     *
     * @return bool
     */
    public function saveSavingsClosing(Session $session, Fund $fund, int $profitAmount): bool
    {
        $closing = Closing::firstOrNew([
            'type' => Closing::TYPE_SAVINGS,
            'session_id' => $session->id,
            'fund_id' => $fund->id,
        ], []);
        $closing->profit = $profitAmount;
        $closing->save();
        return $closing->id > 0;
    }

    /**
     * Delete a savings closing.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return void
     */
    public function deleteSavingsClosing(Session $session, Fund $fund)
    {
        Closing::savings()
            ->where('session_id', $session->id)
            ->where('fund_id', $fund->id)
            ->delete();
    }

    /**
     * Get the savings closings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSavingsClosings(Session $session): Collection
    {
        return Closing::savings()->where('session_id', $session->id)->get();
    }

    /**
     * Check if the given session is closing the savings for the given fund.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return Closing|null
     */
    public function getSavingsClosing(Session $session, Fund $fund): ?Closing
    {
        return Closing::savings()
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
        $closing = $this->getSavingsClosing($session, $fund);

        return $closing?->profit ?? 0;
    }
}
