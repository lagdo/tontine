<?php

namespace Siak\Tontine\Service\Report;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\TenantService;

use function collect;
use function count;
use function is_array;

class SessionService
{
    use Traits\Queries;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @param TenantService $tenantService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(TenantService $tenantService, BalanceCalculator $balanceCalculator)
    {
        $this->tenantService = $tenantService;
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getReceivables(Session $session): Collection
    {
        return $session->round->pools()
            ->withCount([
                'subscriptions as total_count' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid_count' => function($query) use($session) {
                    $query->whereHas('receivables', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('deposit');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                $pool->total_amount = $pool->amount * $pool->total_count;
                $pool->paid_amount = $this->balanceCalculator->getPoolDepositAmount($pool, $session);
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getPayables(Session $session): Collection
    {
        return $session->round->pools()
            ->withCount([
                'subscriptions as total_count' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                },
                'subscriptions as paid_count' => function($query) use($session) {
                    $query->whereHas('payable', function($query) use($session) {
                        $query->where('session_id', $session->id)->whereHas('remitment');
                    });
                },
            ])
            ->get()
            ->each(function($pool) use($session) {
                $sessionCount = $this->tenantService->countEnabledSessions($pool);
                $pool->total_amount = $pool->amount * $pool->total_count * $sessionCount;
                $pool->paid_amount = $this->balanceCalculator->getPoolRemitmentAmount($pool, $session);
            });
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getAuctions(Session $session): Collection
    {
        return DB::table('auctions')
            ->select(DB::raw('sum(auctions.amount) as total'), 'subscriptions.pool_id')
            ->join('remitments', 'auctions.remitment_id', '=', 'remitments.id')
            ->join('payables', 'remitments.payable_id', '=', 'payables.id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->where('auctions.session_id', $session->id)
            ->where('paid', true)
            ->groupBy('subscriptions.pool_id')
            ->pluck('total', 'pool_id');
    }

    /**
     * @param Collection $chargeIds
     * @param Collection $sessionIds
     *
     * @return Collection
     */
    public function getDisbursedAmounts(Collection $chargeIds, Collection $sessionIds): Collection
    {
        if($chargeIds->count() === 0)
        {
            return collect();
        }

        return DB::table('disbursements')
            ->select(DB::raw('sum(amount) as total_amount'),
                DB::raw('count(*) as total_count'), 'charge_id')
            ->groupBy('charge_id')
            ->whereIn('charge_id', $chargeIds)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('charge_id');
    }

    /**
     * @param Closure $settlementFilter
     * @param Member $member|null
     *
     * @return Collection
     */
    private function getBills(Closure $settlementFilter, ?Member $member = null): Collection
    {
        $tontineBillsQuery = DB::table('bills')
            ->join('tontine_bills', 'bills.id', '=', 'tontine_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'tontine_bills.charge_id')
            ->groupBy('tontine_bills.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, function($query) use($member) {
                return $query->where('tontine_bills.member_id', $member->id);
            });
        $roundBillsQuery = DB::table('bills')
            ->join('round_bills', 'bills.id', '=', 'round_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'round_bills.charge_id')
            ->groupBy('round_bills.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, function($query) use($member) {
                return $query->where('round_bills.member_id', $member->id);
            });
        $sessionBillsQuery = DB::table('bills')
            ->join('session_bills', 'bills.id', '=', 'session_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'session_bills.charge_id')
            ->groupBy('session_bills.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, function($query) use($member) {
                return $query->where('session_bills.member_id', $member->id);
            });
        $libreBillsQuery = DB::table('bills')
            ->join('libre_bills', 'bills.id', '=', 'libre_bills.bill_id')
            ->select(DB::raw('sum(bills.amount) as total_amount'),
                DB::raw('count(bills.id) as total_count'), 'libre_bills.charge_id')
            ->groupBy('libre_bills.charge_id')
            ->whereExists($settlementFilter)
            ->when($member !== null, function($query) use($member) {
                return $query->where('libre_bills.member_id', $member->id);
            });

        return $tontineBillsQuery
            ->union($roundBillsQuery)
            ->union($sessionBillsQuery)
            ->union($libreBillsQuery)
            ->get()
            ->keyBy('charge_id');
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionCharges(Session $session): Collection
    {
        $settlementFilter = function(Builder $query) use($session) {
            return $query->select(DB::raw(1))
                ->from('settlements')
                ->where('session_id', $session->id)
                ->whereColumn('settlements.bill_id', 'bills.id');
        };
        $bills = $this->getBills($settlementFilter);
        $sessionIds = collect([$session->id]);

        $charges = $this->tenantService->tontine()->charges()->active()->get();
        $disbursements = $this->getDisbursedAmounts($charges->pluck('id'), $sessionIds);

        return $charges->each(function($charge) use($bills, $disbursements) {
            $bill = $bills[$charge->id] ?? null;
            $charge->total_count = $bill ? $bill->total_count : 0;
            $charge->total_amount = $bill ? $bill->total_amount : 0;
            $charge->disbursement = $disbursements[$charge->id] ?? null;
        });
    }

    /**
     * @return int
     */
    private function countActiveMembers(): int
    {
        // The number of active members is saved in the round, so its current
        // value can be retrieved forever, even when the membership will change.
        $tontine = $this->tenantService->tontine();
        $round = $this->tenantService->round();
        if($round->property === null)
        {
            // Create and save the property with the content
            $memberIds = $tontine->members()->active()->pluck('id')->all();
            $round->property()->create(['content' => ['members' => $memberIds]]);

            return count($memberIds);
        }

        $content = $round->property->content;
        if(isset($content['members']) && is_array($content['members']))
        {
            // Return the existing content value
            return count($content['members']);
        }

        // Update the existing content with the members
        $content['members'] = $tontine->members()->active()->pluck('id')->all();
        $round->property->content = $content;
        $round->property->save();

        return count($content['members']);
    }

    /**
     * @param Session $session
     * @param Member $member|null
     *
     * @return Collection
     */
    public function getTotalCharges(Session $session, ?Member $member = null): Collection
    {
        $sessionIds = $this->tenantService->getSessionIds($session);
        $settlementFilter = function(Builder $query) use($sessionIds) {
            return $query->select(DB::raw(1))
                ->from('settlements')
                ->whereIn('session_id', $sessionIds)
                ->whereColumn('settlements.bill_id', 'bills.id');
        };
        $bills = $this->getBills($settlementFilter, $member);

        $charges = $this->tenantService->tontine()->charges()->active()->get();
        $disbursements = $this->getDisbursedAmounts($charges->pluck('id'), $sessionIds);
        if($member !== null)
        {
            // The disbursement part of each member id calculated by dividing each amount
            // by the number of members.
            $memberCount = $this->countActiveMembers();
            foreach($disbursements as $disbursement)
            {
                $disbursement->total_amount /= $memberCount;
            }
        }

        return $charges->each(function($charge) use($bills, $disbursements) {
            $bill = $bills[$charge->id] ?? null;
            $charge->total_count = $bill ? $bill->total_count : 0;
            $charge->total_amount = $bill ? $bill->total_amount : 0;
            $charge->disbursement = $disbursements[$charge->id] ?? null;
        });
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getLoan(Session $session): object
    {
        $loan = DB::table('loans')
            ->join('debts', 'loans.id', '=', 'debts.loan_id')
            ->select('debts.type', DB::raw('sum(debts.amount) as total_amount'))
            ->where('loans.session_id', $session->id)
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');

        return (object)[
            'principal' => $loan[Debt::TYPE_PRINCIPAL] ?? 0,
            'interest' => $loan[Debt::TYPE_INTEREST] ?? 0,
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getRefund(Session $session): object
    {
        $refund = $this->getRefundQuery()
            ->addSelect('debts.type')
            ->where('refunds.session_id', $session->id)
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');
        $partialRefund = DB::table('partial_refunds')
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->where('partial_refunds.session_id', $session->id)
            ->select('debts.type', DB::raw('sum(partial_refunds.amount) as total_amount'))
            ->groupBy('debts.type')
            ->pluck('total_amount', 'type');

        return (object)[
            'principal' => ($refund[Debt::TYPE_PRINCIPAL] ?? 0) +
                ($partialRefund[Debt::TYPE_PRINCIPAL] ?? 0),
            'interest' => ($refund[Debt::TYPE_INTEREST] ?? 0) +
                ($partialRefund[Debt::TYPE_INTEREST] ?? 0),
        ];
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getFunding(Session $session): object
    {
        $funding = DB::table('fundings')
            ->select(DB::raw('sum(amount) as total_amount'), DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$funding->total_amount)
        {
            $funding->total_amount = 0;
        }
        if(!$funding->total_count)
        {
            $funding->total_count = 0;
        }

        return $funding;
    }

    /**
     * @param Session $session
     *
     * @return object
     */
    public function getDisbursement(Session $session): object
    {
        $disbursement = DB::table('disbursements')
            ->select(DB::raw('sum(amount) as total_amount'), DB::raw('count(id) as total_count'))
            ->where('session_id', $session->id)
            ->first();
        if(!$disbursement->total_amount)
        {
            $disbursement->total_amount = 0;
        }
        if(!$disbursement->total_count)
        {
            $disbursement->total_count = 0;
        }

        return $disbursement;
    }
}
