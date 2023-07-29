<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\TenantService;

use function collect;

class RoundService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(TenantService $tenantService, PoolService $poolService)
    {
        $this->tenantService = $tenantService;
        $this->poolService = $poolService;
    }

    /**
     * Get the first tontine.
     *
     * @return Tontine|null
     */
    public function getFirstTontine(): ?Tontine
    {
        return $this->tenantService->user()->tontines()->first();
    }

    /**
     * Get a single tontine.
     *
     * @param int $tontineId    The tontine id
     *
     * @return Tontine|null
     */
    public function getTontine(int $tontineId): ?Tontine
    {
        return $this->tenantService->user()->tontines()->find($tontineId);
    }

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
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $round = $tontine->rounds()->create($values);
            // Create the only and unique pool for free tontine
            if($tontine->is_libre)
            {
                $round->pools()->create([
                    'title' => $round->title, // Same title as the round
                    'amount' => 0, // No fixed amount
                    'notes' => '',
                ]);
            }
        });

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
