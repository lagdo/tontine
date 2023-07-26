<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;

class FineService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
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
     * Get a paginated list of fines.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFines(int $page = 0): Collection
    {
        $fines = $this->tenantService->tontine()->charges()
            ->active()->variable()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fines->take($this->tenantService->getLimit());
            $fines->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $fines->get();
    }

    /**
     * Get the number of fines.
     *
     * @return int
     */
    public function getFineCount(): int
    {
        return $this->tenantService->tontine()->charges()->variable()->count();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionBills(Session $session): Collection
    {
        // Count the session bills
        return DB::table('fine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('fine_bills.session_id', $session->id)
            ->groupBy('charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsBills(Session $session): Collection
    {
        // Count the session bills
        $sessionIds = $this->tenantService->getPreviousSessions($session, false);
        return DB::table('fine_bills')
            ->select('charge_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('fine_bills.session_id', $sessionIds)
            ->groupBy('charge_id')
            ->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getCurrentSessionSettlements(Session $session): Collection
    {
        // Count the session bills
        $query = DB::table('settlements')->select('charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->where('settlements.session_id', $session->id)
            ->groupBy('charge_id');
        return $query->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    private function getPreviousSessionsSettlements(Session $session): Collection
    {
        // Count the session bills
        $sessionIds = $this->tenantService->getPreviousSessions($session, false);
        $query = DB::table('settlements')->select('charge_id',
            DB::raw('count(*) as total'), DB::raw('sum(amount) as amount'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('fine_bills', 'fine_bills.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->groupBy('charge_id');
        return $query->get();
    }

    /**
     * Format the amounts in the settlements
     *
     * @param Collection $settlements
     *
     * @return Collection
     */
    private function formatAmounts(Collection $settlements): Collection
    {
        return $settlements->map(function($settlement) {
            $settlement->amount = $this->localeService->formatMoney((int)$settlement->amount);
            return $settlement;
        });
    }

    /**
     * Get the report of bills
     *
     * @param Session $session
     *
     * @return array
     */
    public function getBills(Session $session): array
    {
        $currentBills = $this->getCurrentSessionBills($session);
        $previousBills = $this->getPreviousSessionsBills($session);
        return [
            'total' => [
                'current' => $currentBills->pluck('total', 'charge_id'),
                'previous' => $previousBills->pluck('total', 'charge_id'),
            ],
        ];
    }

    /**
     * Get the report of settlements
     *
     * @param Session $session
     *
     * @return array
     */
    public function getSettlements(Session $session): array
    {
        $currentSettlements = $this->getCurrentSessionSettlements($session);
        $previousSettlements = $this->getPreviousSessionsSettlements($session);
        return [
            'zero' => $this->localeService->formatMoney(0),
            'total' => [
                'current' => $currentSettlements->pluck('total', 'charge_id'),
                'previous' => $previousSettlements->pluck('total', 'charge_id'),
            ],
            'amount' => [
                'current' => $this->formatAmounts($currentSettlements)->pluck('amount', 'charge_id'),
                'previous' => $this->formatAmounts($previousSettlements)->pluck('amount', 'charge_id'),
            ],
        ];
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyFined|null
     *
     * @return mixed
     */
    private function getMembersQuery(Charge $charge, Session $session, ?bool $onlyFined)
    {
        $query = $this->tenantService->tontine()->members();
        if($onlyFined === false)
        {
            $query->whereDoesntHave('fine_bills', function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            });
        }
        elseif($onlyFined === true)
        {
            $query->whereHas('fine_bills', function($query) use($charge, $session) {
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
        $members = $this->getMembersQuery($charge, $session, $onlyFined);
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $members->with([
            'fine_bills' => function($query) use($charge, $session) {
                $query->where('charge_id', $charge->id)->where('session_id', $session->id);
            },
        ])->orderBy('name', 'asc')->get()->each(function($member) {
            $member->bill = $member->fine_bills->count() > 0 ? $member->fine_bills->first()->bill : null;
        });
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
        return $this->getMembersQuery($charge, $session, $onlyFined)->count();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     * @param int $amount
     *
     * @return void
     */
    public function createFine(Charge $charge, Session $session, int $memberId, int $amount = 0): void
    {
        $member = $this->tenantService->tontine()->members()->find($memberId);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        DB::transaction(function() use($charge, $session, $member, $amount) {
            $bill = Bill::create([
                'charge' => $charge->name,
                'amount' => $charge->has_amount ? $charge->amount : $amount,
                'issued_at' => now(),
            ]);
            $fine = new FineBill();
            $fine->charge()->associate($charge);
            $fine->member()->associate($member);
            $fine->session()->associate($session);
            $fine->bill()->associate($bill);
            $fine->save();
        });
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param int $memberId
     *
     * @return FineBill|null
     */
    public function getBill(Charge $charge, Session $session, int $memberId): ?FineBill
    {
        if(!($member = $this->tenantService->tontine()->members()->find($memberId)))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        return FineBill::with(['bill'])
            ->where('charge_id', $charge->id)
            ->where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();
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
        if(!($fine = $this->getBill($charge, $session, $memberId)))
        {
            return; // throw new MessageException(trans('tontine.bill.errors.not_found'));
        }

        DB::transaction(function() use($fine) {
            $billId = $fine->bill_id;
            $fine->delete();
            Bill::where('id', $billId)->delete();
        });
    }
}
