<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\TenantService;

use function collect;

class RoundService
{
    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(protected TenantService $tenantService,
        protected PoolService $poolService)
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
        $this->tenantService->tontine()->rounds()->create($values);
        return true;
    }

    /**
     * Update a round.
     *
     * @param int $id
     * @param array $values
     *
     * @return int
     */
    public function updateRound(int $id, array $values): int
    {
        return $this->tenantService->tontine()->rounds()->where('id', $id)->update($values);
    }

    /**
     * Delete a round.
     *
     * @param int $id
     *
     * @return void
     */
    public function deleteRound(int $id)
    {
        $this->tenantService->tontine()->rounds()->where('id', $id)->delete();
    }
}
