<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;

class ChargeService
{
    use Events\BillEventTrait;

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
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return mixed
     */
    private function getQuery(Charge $charge, Session $session, ?bool $onlyFined)
    {
        $query = $this->tenantService->tontine()->members();
        if($onlyFined === false)
        {
            $query->whereDoesntHave('bills', function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            });
        }
        elseif($onlyFined === true)
        {
            $query->whereHas('bills', function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            });
        }
        return $query;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Charge $charge, Session $session, ?bool $onlyFined = null, int $page = 0): Collection
    {
        $members = $this->getQuery($charge, $session, $onlyFined);
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $members->withCount(['bills' => function(Builder $query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            }])->get();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return int
     */
    public function getMemberCount(Charge $charge, Session $session, ?bool $onlyFined = null): int
    {
        return $this->getQuery($charge, $session, $onlyFined)->count();
    }

    /**
     * Get a paginated list of charges.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(int $page = 0): Collection
    {
        $charges = $this->tenantService->tontine()->charges();
        if($page > 0 )
        {
            $charges->take($this->tenantService->getLimit());
            $charges->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $charges->get();
    }

    /**
     * Get the number of charges.
     *
     * @return int
     */
    public function getChargeCount(): int
    {
        return $this->tenantService->tontine()->charges()->count();
    }

    /**
     * Get a single charge.
     *
     * @param int $chargeId    The charge id
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->find($chargeId);
    }

    /**
     * Add a new charge.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createCharges(array $values): bool
    {
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $round = $this->tenantService->round();
            $charges = $tontine->charges()->createMany($values);
            // Create charge bills
            foreach($charges as $charge)
            {
                if($charge->is_fee)
                {
                    $this->chargeCreated($charge, $round);
                }
            }
        });

        return true;
    }

    /**
     * Update a charge.
     *
     * @param Charge $charge
     * @param array $values
     *
     * @return int
     */
    public function updateCharge(Charge $charge, array $values): int
    {
        return $charge->update($values);
    }

    /**
     * Delete a charge.
     *
     * @param Charge $charge
     *
     * @return void
     */
    public function deleteCharge(Charge $charge)
    {
        $charge->delete();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return void
     */
    public function createFine(Charge $charge, Session $session, int $memberId): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        $member->bills()->create([
            'name' => $charge->name,
            'amount' => $charge->amount,
            'issued_at' => now(),
            'charge_id' => $charge->id,
            'session_id' => $session->id,
            'round_id' => $this->tenantService->round()->id,
        ]);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return void
     */
    public function deleteFine(Charge $charge, Session $session, int $memberId): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        $member->bills()->where('charge_id', $charge->id)
            ->where('session_id', $session->id)
            ->where('round_id', $this->tenantService->round()->id)
            ->delete();
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakeCharges(int $count): Collection
    {
        return Charge::factory()->count($count)->make([
            'tontine_id' => $this->tenantService->tontine(),
        ]);
    }
}
