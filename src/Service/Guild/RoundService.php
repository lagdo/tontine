<?php

namespace Siak\Tontine\Service\Guild;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lagdo\Facades\Logger;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Planning\BillSyncService;
use Siak\Tontine\Service\Planning\FundSyncService;
use Siak\Tontine\Service\TenantService;

use function trans;

class RoundService
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     * @param FundSyncService $fundSyncService
     * @param BillSyncService $billSyncService
     */
    public function __construct(private TenantService $tenantService,
        private PoolService $poolService, private FundSyncService $fundSyncService,
        private BillSyncService $billSyncService)
    {}

    /**
     * Get a paginated list of rounds in the selected guild.
     *
     * @param Guild $guild
     * @param int $page
     *
     * @return Collection
     */
    public function getRounds(Guild $guild, int $page = 0): Collection
    {
        return $guild->rounds()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of rounds in the selected guild.
     *
     * @param Guild $guild
     *
     * @return int
     */
    public function getRoundCount(Guild $guild): int
    {
        return $guild->rounds()->count();
    }

    /**
     * Get a single round.
     *
     * @param Guild $guild
     * @param int $roundId
     *
     * @return Round|null
     */
    public function getRound(Guild $guild, int $roundId): ?Round
    {
        return $guild->rounds()->find($roundId);
    }

    /**
     * Add a new round.
     *
     * @param Guild $guild
     * @param array $values
     * @param array $options
     *
     * @return bool
     */
    public function createRound(Guild $guild, array $values, array $options): bool
    {
        DB::transaction(function() use($guild, $values, $options) {
            // Without the sessions, the default fund cannot yet be added.
            // We then save the information, so we can do it later.
            $round = $guild->rounds()->create($values);
            $properties = $round->properties;
            $properties['savings']['fund']['default'] = $options['savings'];
            $round->saveProperties($properties);

            // Add all active members to the new round.
            if($options['members'])
            {
                $members = $guild->members()->active()->get()
                    ->map(fn($member) => ['def_id' => $member->id]);
                $members = $round->members()->createMany($members);
                $this->billSyncService->membersEnabled($round, $members);
            }

            // Add all active charges to the new round.
            if($options['charges'])
            {
                $charges = $guild->charges()->active()->get()
                    ->map(fn($charge) => [
                        'name' => $charge->name,
                        'type' => $charge->type,
                        'period' => $charge->period,
                        'amount' => $charge->amount,
                        'lendable' => $charge->lendable,
                        'def_id' => $charge->id,
                    ]);
                $charges = $round->charges()->createMany($charges);
                $this->billSyncService->chargesEnabled($round, $charges);
            }
        });
        return true;
    }

    /**
     * Update a round.
     *
     * @param Guild $guild
     * @param int $roundId
     * @param array $values
     *
     * @return int
     */
    public function updateRound(Guild $guild, int $roundId, array $values): int
    {
        return $guild->rounds()->where('id', $roundId)->update($values);
    }

    /**
     * Delete a round.
     *
     * @param Guild $guild
     * @param int $roundId
     *
     * @return void
     */
    public function deleteRound(Guild $guild, int $roundId)
    {
        // Delete the session. Will fail if there's still some data attached.
        $round = $guild->rounds()->find($roundId);
        try
        {
            DB::transaction(function() use($round) {
                // Delete related funds and bills.
                $this->fundSyncService->roundDeleted($round);
                $this->billSyncService->roundDeleted($round);

                // Delete the associated members.
                $round->members()->delete();
                // Delete the associated charges.
                $round->charges()->delete();

                // Delete the round.
                $round->delete();
            });
        }
        catch(Exception $e)
        {
            Logger::debug('Unable to delete a round.', [
                'error' => $e->getMessage(),
            ]);
            throw new MessageException(trans('tontine.round.errors.delete'));
        }
    }

    /**
     * Find a session.
     *
     * @param Round $round
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(Round $round, int $sessionId): ?Session
    {
        return $round->sessions()->find($sessionId);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getSessionCount(Round $round): int
    {
        return $round->sessions()->count();
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param Round $round
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(Round $round, int $page = 0, bool $orderAsc = true): Collection
    {
        return $round->sessions()
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }
}
