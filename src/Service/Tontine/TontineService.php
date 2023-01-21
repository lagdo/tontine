<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;

use function trans;

class TontineService
{
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
     * @return array
     */
    public function getTontineTypes(): array
    {
        return [
            Tontine::TYPE_MUTUAL => trans('tontine.labels.types.mutual'),
            Tontine::TYPE_FINANCIAL => trans('tontine.labels.types.financial'),
        ];
    }

    /**
     * Get a paginated list of tontines in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getTontines(int $page = 0): Collection
    {
        $tontines = $this->tenantService->user()->tontines();
        if($page > 0 )
        {
            $tontines->take($this->tenantService->getLimit());
            $tontines->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $tontines->get();
    }

    /**
     * Get the number of tontines in the selected round.
     *
     * @return int
     */
    public function getTontineCount(): int
    {
        return $this->tenantService->user()->tontines()->count();
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
     * Get the rounds of the selected tontine.
     *
     * @return Collection
     */
    public function getRounds(): Collection
    {
        $tontine = $this->tenantService->tontine();
        return ($tontine) ? $tontine->rounds()->get() : collect([]);
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
     * Add a new tontine.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createTontine(array $values): bool
    {
        $this->tenantService->user()->tontines()->create($values);

        return true;
    }

    /**
     * Update a tontine.
     *
     * @param int $id
     * @param array $values
     *
     * @return int
     */
    public function updateTontine(int $id, array $values): int
    {
        return $this->tenantService->user()->tontines()->where('id', $id)->update($values);
    }

    /**
     * Delete a tontine.
     *
     * @param int $id
     *
     * @return void
     */
    public function deleteTontine(int $id)
    {
        $this->tenantService->user()->tontines()->where('id', $id)->delete();
    }
}
