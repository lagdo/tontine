<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\TenantService;

use function config;

class TontineService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of tontines in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getTontines(int $page = 0): Collection
    {
        return $this->tenantService->user()->tontines()
            ->page($page, $this->tenantService->getLimit())
            ->get();
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
        $this->tenantService->user()->tontines()->create($values);
        return true;
    }

    /**
     * Update a tontine.
     *
     * @param int $id
     * @param array $values
     *
     * @return bool
     */
    public function updateTontine(int $id, array $values): bool
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
        $tontine = $this->tenantService->user()->tontines()->find($id);
        if(!$tontine)
        {
            return;
        }
        DB::transaction(function() use($tontine) {
            $tontine->members()->delete();
            $tontine->rounds()->delete();
            $tontine->charges()->delete();
            $tontine->delete();
        });
    }

    /**
     * Get the tontine options
     *
     * @return array
     */
    public function getTontineOptions(): array
    {
        return $this->tenantService->tontine()->properties;
    }

    /**
     * Get the report template name
     *
     * @return string
     */
    public function getReportTemplate(): string
    {
        $options = $this->getTontineOptions();
        return $options['reports']['template'] ?? config('tontine.templates.report', 'default');
    }

    /**
     * Save the tontine options
     *
     * @param array $options
     *
     * @return void
     */
    public function saveTontineOptions(array $options)
    {
        $tontine = $this->tenantService->tontine();
        $properties = $tontine->properties;
        $properties['reports'] = $options['reports'];
        $tontine->saveProperties($properties);
    }
}
