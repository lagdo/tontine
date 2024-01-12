<?php

namespace Siak\Tontine\Service\Planning;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\Planning\RoundValidator;

use function collect;
use function trans;

class RoundService
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     * @param RoundValidator $validator
     */
    public function __construct(protected TenantService $tenantService,
        protected PoolService $poolService, protected RoundValidator $validator)
    {}

    /**
     * Get a paginated list of rounds in the selected tontine.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getRounds(int $page = 0): Collection
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return collect([]);
        }
        return $tontine->rounds()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of rounds in the selected tontine.
     *
     * @return int
     */
    public function getRoundCount(): int
    {
        $tontine = $this->tenantService->tontine();
        return !$tontine ? 0 : $tontine->rounds()->count();
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
        return $this->tenantService->tontine()->rounds()->find($roundId);
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
        $this->tenantService->tontine()->rounds()->create($values);
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
        return $this->tenantService->tontine()
            ->rounds()
            ->where('id', $roundId)
            ->update($values);
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
                $tontine = $this->tenantService->tontine();
                $tontine->sessions()->where('round_id', $roundId)->delete();
                $tontine->rounds()->where('id', $roundId)->delete();
            });
        }
        catch(Exception $e)
        {
            throw new MessageException(trans('tontine.round.errors.delete'));
        }
    }
}
