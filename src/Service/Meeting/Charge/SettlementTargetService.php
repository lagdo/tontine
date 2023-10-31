<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\SettlementTarget;
use Siak\Tontine\Service\TenantService;

use function trans;

class SettlementTargetService
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
     * Get the sessions after the current.
     *
     * @param Session $currentSession
     *
     * @return Collection
     */
    public function getDeadlineSessions(Session $currentSession): Collection
    {
        return $this->tenantService->getSessions()
            ->filter(function($session) use($currentSession) {
                return $session->start_at > $currentSession->start_at;
            })
            ->pluck('title', 'id');
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return SettlementTarget|null
     */
    public function getTarget(Charge $charge, Session $session): ?SettlementTarget
    {
        return $charge->targets()
            ->with(['session', 'deadline'])
            ->get()
            ->filter(function($target) use($session) {
                return $session->start_at >= $target->session->start_at &&
                    $session->start_at <= $target->deadline->start_at;
            })
            ->first();
    }

    /**
     * @param string $search
     *
     * @return mixed
     */
    private function getMembersQuery(string $search = '')
    {
        return $this->tenantService->tontine()->members()->active()
            ->when($search !== '', function($query) use($search) {
                return $query->where(DB::raw('lower(members.name)'),
                    'like', '%' . strtolower($search) . '%');
            });
    }

    /**
     * @param Charge $charge
     * @param SettlementTarget $target
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembersWithSettlements(Charge $charge, SettlementTarget $target,
        string $search = '', int $page = 0): Collection
    {
        $sessions = $this->tenantService->round()->sessions
            ->filter(function($session) use($target) {
                return $session->start_at >= $target->session->start_at &&
                    $session->start_at <= $target->deadline->start_at;
            });
        return $this->getMembersQuery($search)
            ->select('members.id', 'members.name')
            ->addSelect([
                'paid' => DB::table('settlements')
                    ->join('bills', 'settlements.bill_id', '=', 'bills.id')
                    ->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
                    ->whereColumn('libre_bills.member_id', 'members.id')
                    ->where('libre_bills.charge_id', $charge->id)
                    ->whereIn('settlements.session_id', $sessions->pluck('id'))
                    ->select(DB::raw('SUM(bills.amount)'))
            ])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('members.name', 'asc')
            ->get();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param string $search
     * @param bool $filter|null
     *
     * @return int
     */
    public function getMemberCount(string $search = ''): int
    {
        return $this->getMembersQuery($search)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param Session $deadline
     * @param float $amount
     * @param bool $global
     *
     * @return void
     */
    public function createTarget(Charge $charge, Session $session,
        Session $deadline, float $amount, bool $global): void
    {
        $target = new SettlementTarget();
        $target->amount = $amount;
        $target->global = $global;
        $target->charge()->associate($charge);
        $target->session()->associate($session);
        $target->deadline()->associate($deadline);
        $target->save();
    }

    /**
     * @param SettlementTarget $target
     * @param Session $session
     * @param Session $deadline
     * @param float $amount
     * @param bool $global
     *
     * @return void
     */
    public function updateTarget(SettlementTarget $target, Session $session,
        Session $deadline, float $amount, bool $global): void
    {
        $target->amount = $amount;
        $target->global = $global;
        $target->session()->associate($session);
        $target->deadline()->associate($deadline);
        $target->save();
    }

    /**
     * @param SettlementTarget $target
     *
     * @return void
     */
    public function deleteTarget(SettlementTarget $target): void
    {
        $target->delete();
    }
}
