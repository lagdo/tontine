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
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\Guild\RoundValidator;

use function collect;
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
     * @param int $page
     *
     * @return Collection
     */
    public function getRounds(int $page = 0): Collection
    {
        if(!($guild = $this->tenantService->guild()))
        {
            return collect([]);
        }
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
     * @return int
     */
    public function getRoundCount(): int
    {
        $guild = $this->tenantService->guild();
        return !$guild ? 0 : $guild->rounds()->count();
    }

    /**
     * Get a single round.
     *
     * @param int $roundId    The round id
     *
     * @return Round|null
     */
    public function getRound(int $roundId): ?Round
    {
        return $this->tenantService->guild()->rounds()->find($roundId);
    }

    /**
     * Add a new round.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createRound(array $values): bool
    {
        $values = $this->validator->validateItem($values);
        DB::transaction(function() use($values) {
            $round = $this->tenantService->guild()->rounds()
                ->create(Arr::except($values, 'savings'));
            $properties = $round->properties;
            $properties['savings']['fund']['default'] = $values['savings'];
            $round->saveProperties($properties);
        });
        return true;
    }

    /**
     * Update a round.
     *
     * @param int $roundId
     * @param array $values
     *
     * @return int
     */
    public function updateRound(int $roundId, array $values): int
    {
        $values = $this->validator->validateItem($values);
        return DB::transaction(function() use($roundId, $values) {
            $round = $this->tenantService->guild()->rounds()->find($roundId);
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
     * @param int $roundId
     *
     * @return void
     */
    public function deleteRound(int $roundId)
    {
        // Delete the session. Will fail if there's still some data attached.
        try
        {
            DB::transaction(function() use($roundId) {
                // Delete the round and all the related sessions.
                $guild = $this->tenantService->guild();
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
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }
}
