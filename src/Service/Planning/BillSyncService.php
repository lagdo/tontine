<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\OnetimeBill;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\Session;
use DateTime;

use function collect;
use function count;
use function now;

class BillSyncService
{
    /**
     * @var DateTime
     */
    private DateTime $today;

    /**
     * @var array
     */
    private array $existingBills;

    /**
     * @var array
     */
    private array $newBills;

    /**
     * @param Collection $charges
     * @param Collection $members
     *
     * @return void
     */
    private function initBills(Collection $charges, Collection $members): void
    {
        $this->today = now();
        $this->newBills = [
            'onetime_bills' => [],
            'round_bills' => [],
            'session_bills' => [],
        ];
        $this->existingBills = [
            'onetime_bills' => [],
            'round_bills' => [],
        ];

        // Avoid duplicates creation.
        $this->existingBills['onetime_bills'] = OnetimeBill::select([
                DB::raw('charges.def_id as charge_def_id'),
                DB::raw('members.def_id as member_def_id'),
            ])
            ->where(fn($qw) => $qw
                // Take the bills in the current round.
                ->orWhere(fn($qwb) => $qwb
                    ->whereIn('charge_id', $charges->pluck('id'))
                    ->whereIn('member_id', $members->pluck('id')))
                // Take the onetime bills already paid, including in other rounds.
                ->orWhere(fn($qwd) => $qwd
                    ->whereHas('charge', fn($qc) =>
                        $qc->once()
                            ->whereIn('def_id', $charges->pluck('def_id')))
                    ->whereHas('member', fn($qm) =>
                        $qm->whereIn('def_id', $members->pluck('def_id')))
                    ->whereHas('bill', fn($qb) => $qb->whereHas('settlement')))
            )
            ->join('charges', 'charges.id', '=', 'onetime_bills.charge_id')
            ->join('members', 'members.id', '=', 'onetime_bills.member_id')
            ->distinct()
            ->get();
        $this->existingBills['round_bills'] = RoundBill::select(['charge_id', 'member_id'])
            ->whereIn('charge_id', $charges->pluck('id'))
            ->whereIn('member_id', $members->pluck('id'))
            ->get();
    }

    /**
     * @return void
     */
    private function saveBills(): void
    {
        foreach(['onetime_bills', 'round_bills', 'session_bills'] as $table)
        {
            if(count($this->newBills[$table]) > 0)
            {
                DB::table($table)->insert($this->newBills[$table]);
            }
        }
    }

    /**
     * @param Charge $charge
     *
     * @return Bill
     */
    private function createBill(Charge $charge): Bill
    {
        return Bill::create([
            'charge' => $charge->def->name,
            'amount' => $charge->def->amount,
            'lendable' => $charge->def->lendable,
            'issued_at' => $this->today,
        ]);
    }

    /**
     * @param Charge $charge
     * @param Member $member
     *
     * @return void
     */
    private function createOnetimeBill(Charge $charge, Member $member): void
    {
        if($this->existingBills['onetime_bills']
            ->contains(fn(OnetimeBill $bill) =>
                $bill->charge_def_id === $charge->def_id &&
                $bill->member_def_id === $member->def_id))
        {
            return;
        }

        $bill = $this->createBill($charge);
        $this->newBills['onetime_bills'][] = [
            'bill_id' => $bill->id,
            'charge_id' => $charge->id,
            'member_id' => $member->id,
        ];
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     *
     * @return void
     */
    private function createRoundBill(Charge $charge, Member $member, Round $round): void
    {
        if($this->existingBills['round_bills']
            ->contains(fn(RoundBill $bill) =>
                $bill->charge_id === $charge->id &&
                $bill->member_id === $member->id))
        {
            return;
        }

        $bill = $this->createBill($charge);
        $this->newBills['round_bills'][] = [
            'bill_id' => $bill->id,
            'charge_id' => $charge->id,
            'member_id' => $member->id,
            'round_id' => $round->id,
        ];
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Session $session
     *
     * @return void
     */
    private function createSessionBill(Charge $charge, Member $member, Session $session): void
    {
        $bill = $this->createBill($charge);
        $this->newBills['session_bills'][] = [
            'bill_id' => $bill->id,
            'charge_id' => $charge->id,
            'member_id' => $member->id,
            'session_id' => $session->id,
        ];
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     * @param Collection|array $sessions
     *
     * @return void
     */
    private function createBills(Charge $charge, Member $member,
        Round $round, Collection|array $sessions): void
    {
        if($charge->def->period_once)
        {
            $this->createOnetimeBill($charge, $member);
            return;
        }
        if($charge->def->period_round)
        {
            $this->createRoundBill($charge, $member, $round);
            return;
        }
        foreach($sessions as $session)
        {
            $this->createSessionBill($charge, $member, $session);
        }
    }

    /**
     * @param Charge|Member $owner
     * @param string $foreignKey
     *
     * @return void
     */
    private function deleteBills(Charge|Member $owner, string $foreignKey): void
    {
        $billIds = DB::table('onetime_bills')
            ->where($foreignKey, $owner->id)
            ->select('bill_id')
            ->union(DB::table('round_bills')
                ->where($foreignKey, $owner->id)
                ->select('bill_id'))
            ->union(DB::table('session_bills')
                ->where($foreignKey, $owner->id)
                ->select('bill_id'))
            ->union(DB::table('libre_bills')
                ->where($foreignKey, $owner->id)
                ->select('bill_id'))
            ->pluck('bill_id');
        if($billIds->count() === 0)
        {
            return;
        }

        // Will fail if a settlement exists for any of those bills.
        $owner->onetime_bills()->delete();
        $owner->round_bills()->delete();
        $owner->session_bills()->delete();
        $owner->libre_bills()->delete();
        DB::table('bills')->whereIn('id', $billIds)->delete();
    }

    /**
     * @param Round $round
     * @param Charge $charge
     *
     * @return void
     */
    public function chargeEnabled(Round $round, Charge $charge): void
    {
        if($charge->def->is_fine || $charge->def->is_variable || $round->members->count() === 0)
        {
            return;
        }

        $this->initBills(collect([$charge]), $round->members);
        foreach($round->members as $member)
        {
            $this->createBills($charge, $member, $round, $round->sessions);
        }
        $this->saveBills();
    }

    /**
     * @param Round $round
     * @param Charge $charge
     *
     * @return void
     */
    public function chargeRemoved(Round $round, Charge $charge): void
    {
        $this->deleteBills($charge, 'charge_id');
    }

    /**
     * @param Round $round
     * @param Member $member
     *
     * @return void
     */
    public function memberEnabled(Round $round, Member $member): void
    {
        $charges = $round->charges->filter(fn($charge) =>
            $charge->def->is_fee && $charge->def->is_fixed);
        if($charges->count() === 0)
        {
            return;
        }

        $this->initBills($charges, collect([$member]));
        foreach($charges as $charge)
        {
            $this->createBills($charge, $member, $round, $round->sessions);
        }
        $this->saveBills();
    }

    /**
     * @param Round $round
     * @param Member $member
     *
     * @return void
     */
    public function memberRemoved(Round $round, Member $member): void
    {
        $this->deleteBills($member, 'member_id');
    }

    /**
     * @param Round $round
     * @param Collection|array $sessions
     *
     * @return void
     */
    public function sessionsCreated(Round $round, Collection|array $sessions): void
    {
        $charges = $round->charges->filter(fn($charge) =>
            $charge->def->is_fee && $charge->def->is_fixed);
        if($charges->count() === 0 || $round->members->count() === 0)
        {
            return;
        }

        $this->initBills($charges, $round->members);
        foreach($charges as $charge)
        {
            foreach($round->members as $member)
            {
                $this->createBills($charge, $member, $round, $sessions);
            }
        }
        $this->saveBills();
    }

    /**
     * @param Session $session
     *
     * @return void
     */
    public function sessionDeleted(Session $session)
    {
        $billIds = DB::table('session_bills')
            ->where('session_id', $session->id)
            ->select('bill_id')
            ->union(DB::table('libre_bills')
                ->where('session_id', $session->id)
                ->select('bill_id'))
            ->pluck('bill_id');
        // Will fail if a settlement exists for any of those bills.
        $session->session_bills()->delete();
        $session->libre_bills()->delete();
        DB::table('bills')->whereIn('id', $billIds)->delete();
    }
}
