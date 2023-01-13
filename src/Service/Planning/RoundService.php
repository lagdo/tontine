<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Events\EventTrait;

use function collect;

class RoundService
{
    use EventTrait;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
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
     * Get a paginated list of rounds in the selected round.
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
        $rounds = $tontine->rounds();
        if($page > 0 )
        {
            $rounds->take($this->tenantService->getLimit());
            $rounds->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $rounds->get();
    }

    /**
     * Get the number of rounds in the selected round.
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
     * Open a round.
     *
     * @param Round $round
     *
     * @return void
     */
    public function openRound(Round $round)
    {
        if($round->is_opened)
        {
            return;
        }
        DB::transaction(function() use($round) {
            $round->update(['status' => RoundModel::STATUS_OPENED]);
            $this->roundOpened($this->tenantService->tontine(), $round);
        });
    }

    /**
     * Close a round.
     *
     * @param Round $round
     *
     * @return void
     */
    public function closeRound(Round $round)
    {
        $round->update(['status' => RoundModel::STATUS_CLOSED]);
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
