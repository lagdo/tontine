<?php

namespace Siak\Tontine\Service\Guild;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Planning\DataSyncService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\Guild\RoundValidator;

use function trans;

class RoundService
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     * @param RoundValidator $validator
     * @param DataSyncService $dataSyncService
     */
    public function __construct(protected TenantService $tenantService,
        protected PoolService $poolService, protected RoundValidator $validator,
        private DataSyncService $dataSyncService)
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
     * Get a list of rounds for the dropdown select.
     *
     * @param Guild $guild
     *
     * @return Collection
     */
    public function getRoundList(Guild $guild): Collection
    {
        // Only rounds with at least 2 sessions are selectable.
        return $guild->rounds()
            ->join('sessions', 'sessions.round_id', '=', 'rounds.id')
            ->select('rounds.title', 'rounds.id', DB::raw('count(sessions.id)'))
            ->groupBy('rounds.title', 'rounds.id')
            ->havingRaw('count(sessions.id) > ?', [1])
            ->pluck('title', 'id');
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
     *
     * @return bool
     */
    public function createRound(Guild $guild, array $values): bool
    {
        $values = $this->validator->validateItem($values);
        DB::transaction(function() use($guild, $values) {
            $round = $guild->rounds()->create(Arr::except($values, 'savings'));
            $properties = $round->properties;
            $properties['savings']['fund']['default'] = $values['savings'];
            $round->saveProperties($properties);
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
        $values = $this->validator->validateItem($values);
        $round = $guild->rounds()->find($roundId);
        return DB::transaction(function() use($round, $values) {
            $properties = $round->properties;
            $properties['savings']['fund']['default'] = $values['savings'];
            $round->saveProperties($properties);

            // Create the default savings fund.
            $this->dataSyncService->saveDefaultFund($round);

            return $round->update(Arr::except($values, 'savings'));
        });
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
        try
        {
            DB::transaction(function() use($guild, $roundId) {
                // Delete the round and all the related sessions.
                $guild->sessions()->where('round_id', $roundId)->delete();
                $guild->rounds()->where('id', $roundId)->delete();
                $guild->default_fund->funds()->where('round_id', $roundId)->delete();
            });
        }
        catch(Exception $e)
        {
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
