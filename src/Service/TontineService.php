<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Country;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Tontine;

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
     * Add a new tontine.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createTontine(array $values): bool
    {
        // The default country and currency
        $values['currency_id'] = Currency::where('code', 'XAF')->first()->id;
        $values['country_id'] = Country::where('code', 'CM')->first()->id;
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
