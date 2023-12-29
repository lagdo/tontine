<?php

namespace Siak\Tontine\Service\Traits;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

use function now;

trait EventTrait
{
    /**
     * @param Charge $charge
     * @param DateTime $today
     *
     * @return Bill
     */
    private function createBill(Charge $charge, DateTime $today): Bill
    {
        return Bill::create([
            'charge' => $charge->name,
            'amount' => $charge->amount,
            'lendable' => $charge->lendable,
            'issued_at' => $today,
        ]);
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param DateTime $today
     *
     * @return void
     */
    private function createTontineBill(Charge $charge, Member $member, DateTime $today)
    {
        $bill = $this->createBill($charge, $today);
        $tontineBill = new TontineBill();
        $tontineBill->bill()->associate($bill);
        $tontineBill->charge()->associate($charge);
        $tontineBill->member()->associate($member);
        $tontineBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     * @param DateTime $today
     *
     * @return void
     */
    private function createRoundBill(Charge $charge, Member $member, Round $round, DateTime $today)
    {
        $bill = $this->createBill($charge, $today);
        $roundBill = new RoundBill();
        $roundBill->bill()->associate($bill);
        $roundBill->charge()->associate($charge);
        $roundBill->member()->associate($member);
        $roundBill->round()->associate($round);
        $roundBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Session $session
     * @param DateTime $today
     *
     * @return void
     */
    private function createSessionBill(Charge $charge, Member $member, Session $session, DateTime $today)
    {
        $bill = $this->createBill($charge, $today);
        $sessionBill = new SessionBill();
        $sessionBill->bill()->associate($bill);
        $sessionBill->charge()->associate($charge);
        $sessionBill->member()->associate($member);
        $sessionBill->session()->associate($session);
        $sessionBill->save();
    }

    /**
     * @param Tontine $tontine
     * @param Charge $charge
     *
     * @return void
     */
    protected function chargeCreated(Tontine $tontine, Charge $charge)
    {
        if(!$charge->period_once)
        {
            return;
        }
        $today = now();
        // Create a tontine bill for each member
        foreach($tontine->members()->get() as $member)
        {
            $this->createTontineBill($charge, $member, $today);
        }
    }

    /**
     * @param Tontine $tontine
     * @param Member $member
     *
     * @return void
     */
    protected function memberCreated(Tontine $tontine, Member $member)
    {
        $today = now();
        // Create a tontine bill for each charge
        foreach($tontine->charges()->active()->once()->get() as $charge)
        {
            $this->createTontineBill($charge, $member, $today);
        }
    }

    /**
     * @param Tontine $tontine
     * @param Round $round
     *
     * @return void
     */
    protected function roundOpened(Tontine $tontine, Round $round)
    {
        $today = now();
        $members = $tontine->members()->with([
            'tontine_bills',
            'round_bills' => function($query) use($round) {
                $query->where('round_id', $round->id);
            },
        ])->get();
        $roundCharges = $tontine->charges()->active()->round()->get();
        // Create a round bill for each member
        foreach($members as $member)
        {
            foreach($roundCharges as $charge)
            {
                $count = $member->round_bills->filter(function($bill) use($charge) {
                    return $bill->charge_id = $charge->id;
                })->count();
                if($count === 0)
                {
                    $this->createRoundBill($charge, $member, $round, $today);
                }
            }
        }
        $tontineCharges = $tontine->charges()->active()->once()->get();
        // Create a tontine bill for each member
        foreach($members as $member)
        {
            foreach($tontineCharges as $charge)
            {
                $count = $member->tontine_bills->filter(function($bill) use($charge) {
                    return $bill->charge_id = $charge->id;
                })->count();
                if($count === 0)
                {
                    $this->createTontineBill($charge, $member, $today);
                }
            }
        }
    }

    /**
     * @param Tontine $tontine
     * @param Session $session
     *
     * @return void
     */
    protected function sessionOpened(Tontine $tontine, Session $session)
    {
        // Make sure the round is also opened.
        $this->roundOpened($tontine, $session->round);

        $today = now();
        $members = $tontine->members()->with([
            'session_bills' => function($query) use($session) {
                $query->where('session_id', $session->id);
            },
        ])->get();
        $sessionCharges = $tontine->charges()->active()->session()->get();

        // Sync the session bills for each member and each session charge
        foreach($sessionCharges as $charge)
        {
            foreach($members as $member)
            {
                $count = $member->session_bills->filter(function($bill) use($charge) {
                    return $bill->charge_id = $charge->id;
                })->count();
                if($count === 0)
                {
                    $this->createSessionBill($charge, $member, $session, $today);
                }
            }
        };

        // Sync the receivables for each subscription on each pool
        $pools = Pool::ofSession($session)->get();
        foreach($pools as $pool)
        {
            if($session->enabled($pool))
            {
                $subscriptions = $pool->subscriptions()
                    ->whereDoesntHave('receivables', function(Builder $query) use($session) {
                        return $query->where('session_id', $session->id);
                    });
                foreach($subscriptions->get() as $subscription)
                {
                    $subscription->receivables()->create(['session_id' => $session->id]);
                }
            }
        }
    }
}
