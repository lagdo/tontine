<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\OnetimeBill;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\Session;
use Symfony\Component\Uid\Ulid;
use DateTime;

use function array_map;
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
     * @var Ulid
     */
    private Ulid $bulkId;

    /**
     * @var int
     */
    private int $bulkRank;

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
     * @param Round $round
     *
     * @return void
     */
    private function initBills(Collection $charges, Collection $members, Round $round): void
    {
        $this->today = now();
        $this->bulkId = Str::ulid();
        $this->bulkRank = 0;

        $this->newBills = [
            'bills' => [],
            'onetime_bills' => [],
            'round_bills' => [],
            'session_bills' => [],
        ];
        // Avoid duplicates creation.
        $this->existingBills = [
            // Take the onetime bills paid in other rounds/
            'onetime_bills' => DB::table(DB::raw('v_paid_onetime_bills as b'))
                ->join(DB::raw('charges as c'), 'c.def_id', '=', 'b.charge_def_id')
                ->join(DB::raw('members as m'), 'm.def_id', '=', 'b.member_def_id')
                ->select([
                    DB::raw('c.id as charge_id'),
                    DB::raw('m.id as member_id'),
                ])
                ->whereIn('c.id', $charges->pluck('id'))
                ->whereIn('m.id', $members->pluck('id'))
                ->where('b.round_id', '!=', $round->id)
                ->get()
                // And those in the current round.
                ->concat(OnetimeBill::select(['charge_id', 'member_id'])
                    ->whereIn('charge_id', $charges->pluck('id'))
                    ->whereIn('member_id', $members->pluck('id'))
                    ->get()),
            'round_bills' => RoundBill::select(['charge_id', 'member_id'])
                ->whereIn('charge_id', $charges->pluck('id'))
                ->whereIn('member_id', $members->pluck('id'))
                ->get(),
        ];
    }

    /**
     * @return void
     */
    private function saveBills(): void
    {
        // Save the bills table entries.
        DB::table('bills')->insert($this->newBills['bills']);
        // Get the ids fom the bills table.
        $billIds = DB::table('bills')
            ->where('bulk_id', $this->bulkId)
            ->pluck('id', 'bulk_rank');
        // Save the related tables entries.
        foreach(['onetime_bills', 'round_bills', 'session_bills'] as $table)
        {
            if(count($this->newBills[$table]) > 0)
            {
                // Replace the bill ranks with the bill ids.
                $newBills = array_map(function(array $bill) use($billIds) {
                    $bill['bill_id'] = $billIds[$bill['bill_id']];
                    return $bill;
                }, $this->newBills[$table]);
                DB::table($table)->insert($newBills);
            }
        }
    }

    /**
     * @param Charge $charge
     * @param string $table
     * @param array $billData
     *
     * @return void
     */
    private function createBill(Charge $charge, string $table, array $billData): void
    {
        $bulkRank = ++$this->bulkRank;
        $this->newBills['bills'][] = [
            'charge' => $charge->def->name,
            'amount' => $charge->def->amount,
            'lendable' => $charge->def->lendable,
            'issued_at' => $this->today,
            'bulk_id' => $this->bulkId,
            'bulk_rank' => $bulkRank,
        ];
        // The bull rank is temporarily saved in the bill_id field
        $billData['bill_id'] = $bulkRank;
        $this->newBills[$table][] = $billData;
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     *
     * @return void
     */
    private function createOnetimeBill(Charge $charge, Member $member, Round $round): void
    {
        if($this->existingBills['onetime_bills']->contains(fn($bill) =>
            $bill->charge_id === $charge->id &&
            $bill->member_id === $member->id))
        {
            return;
        }

        $this->createBill($charge, 'onetime_bills', [
            'charge_id' => $charge->id,
            'member_id' => $member->id,
            'round_id' => $round->id,
        ]);
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
        if($this->existingBills['round_bills']->contains(fn($bill) =>
            $bill->charge_id === $charge->id &&
            $bill->member_id === $member->id))
        {
            return;
        }

        $this->createBill($charge, 'round_bills', [
            'charge_id' => $charge->id,
            'member_id' => $member->id,
            'round_id' => $round->id,
        ]);
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
        $this->createBill($charge, 'session_bills', [
            'charge_id' => $charge->id,
            'member_id' => $member->id,
            'session_id' => $session->id,
        ]);
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
            $this->createOnetimeBill($charge, $member, $round);
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

        $this->initBills(collect([$charge]), $round->members, $round);
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

        $this->initBills($charges, collect([$member]), $round);
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

        $this->initBills($charges, $round->members, $round);
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
