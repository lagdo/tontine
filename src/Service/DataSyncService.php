<?php

namespace Siak\Tontine\Service;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\OnetimeBill;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\SessionBill;
use DateTime;
use Closure;

use function trans;
use function now;

class DataSyncService
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
    private function createOnetimeBill(Charge $charge, Member $member, DateTime $today): void
    {
        $bill = $this->createBill($charge, $today);
        $onetimeBill = new OnetimeBill();
        $onetimeBill->bill()->associate($bill);
        $onetimeBill->charge()->associate($charge);
        $onetimeBill->member()->associate($member);
        $onetimeBill->save();
    }

    /**
     * @param Charge $charge
     * @param Member $member
     * @param Round $round
     * @param DateTime $today
     *
     * @return void
     */
    private function createRoundBill(Charge $charge, Member $member, Round $round, DateTime $today): void
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
    private function createSessionBill(Charge $charge, Member $member, Session $session, DateTime $today): void
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
     * @param Guild $guild
     * @param Charge $charge
     *
     * @return void
     */
    public function chargeCreated(Guild $guild, Charge $charge): void
    {
        if(!$charge->period_once)
        {
            return;
        }
        $today = now();
        // Create a one off bill for each member
        foreach($guild->members()->get() as $member)
        {
            $this->createOnetimeBill($charge, $member, $today);
        }
    }

    /**
     * @param Guild $guild
     * @param Member $member
     *
     * @return void
     */
    public function memberCreated(Guild $guild, Member $member): void
    {
        $today = now();
        // Create a one off bill for each charge
        foreach($guild->charges()->active()->once()->get() as $charge)
        {
            $this->createOnetimeBill($charge, $member, $today);
        }
    }

    /**
     * @param Guild $guild
     * @param Round $round
     *
     * @return void
     */
    public function roundSynced(Guild $guild, Round $round): void
    {
        $today = now();
        $members = $guild
            ->members()
            ->with([
                'onetime_bills',
                'round_bills' => fn($query) => $query->where('round_id', $round->id),
            ])
            ->get();
        $roundCharges = $guild->charges()->active()->round()->get();
        // Create a round bill for each member
        foreach($members as $member)
        {
            foreach($roundCharges as $charge)
            {
                $count = $member->round_bills
                    ->filter(fn($bill) => $bill->charge_id === $charge->id)
                    ->count();
                if($count === 0)
                {
                    $this->createRoundBill($charge, $member, $round, $today);
                }
            }
        }
        $guildCharges = $guild->charges()->active()->once()->get();
        // Create a one off bill for each member
        foreach($members as $member)
        {
            foreach($guildCharges as $charge)
            {
                $count = $member->onetime_bills
                    ->filter(fn($bill) => $bill->charge_id === $charge->id)
                    ->count();
                if($count === 0)
                {
                    $this->createOnetimeBill($charge, $member, $today);
                }
            }
        }

        // Create the default savings fund.
        $this->saveDefaultFund($round);
    }

    /**
     * @param Guild $guild
     * @param Session $session
     *
     * @return void
     */
    public function sessionSynced(Guild $guild, Session $session): void
    {
        // Make sure the round is also opened.
        $this->roundSynced($guild, $session->round);

        $today = now();
        $members = $guild
            ->members()
            ->with([
                'session_bills' => fn($query) => $query->where('session_id', $session->id),
            ])
            ->get();
        $sessionCharges = $guild->charges()->active()->session()->get();

        // Sync the session bills for each member and each session charge
        foreach($sessionCharges as $charge)
        {
            foreach($members as $member)
            {
                $count = $member->session_bills
                    ->filter(fn($bill) => $bill->charge_id === $charge->id)
                    ->count();
                if($count === 0)
                {
                    $this->createSessionBill($charge, $member, $session, $today);
                }
            }
        };

        // Sync the receivables for each subscription on each pool
        $pools = $session->pools()->get();
        foreach($pools as $pool)
        {
            $subscriptions = $pool
                ->subscriptions()
                ->whereDoesntHave('receivables',
                    fn(Builder $query) => $query->where('session_id', $session->id));
            foreach($subscriptions->get() as $subscription)
            {
                $subscription->receivables()->create(['session_id' => $session->id]);
            }
        }
    }

    /**
     * @param string $table
     * @param string $relation
     *
     * @return Closure
     */
    private function filter(string $table, string $relation): Closure
    {
        return function($query) use($relation, $table) {
            $query->select(DB::raw(1))
                ->from($relation)
                ->whereColumn("$relation.session_id", "$table.session_id")
                ->whereColumn("$relation.pool_id", 'subscriptions.pool_id');
        };
    }

    /**
     * @param string $table
     *
     * @return Closure
     */
    private function filters(string $table): Closure
    {
        return function($query) use($table) {
            $query->orWhereNotExists($this->filter($table, 'v_pool_session'))
                ->orWhereExists($this->filter($table, 'pool_session_disabled'));
        };
    }

    /**
     * @param Round $round
     * @param Pool $pool
     *
     * @return void
     */
    public function savePoolFund(Round $round, Pool $pool): void
    {
        Fund::updateOrCreate([
            'pool_id' => $pool->id,
        ], [
            'def_id' => $round->guild->default_fund->id,
            'round_id' => $round->id,
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
            'interest_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    public function saveDefaultFund(Round $round): void
    {
        if(!$round->start || !$round->end)
        {
            return;
        }

        Pool::ofRound($round)
            ->whereHas('def', fn($q) => $q->depositLendable())
            ->get()
            ->each(fn($pool) => $this->savePoolFund($round, $pool));

        $fundId = $round->guild->default_fund->id;
        if(!$round->add_default_fund)
        {
            Fund::where('round_id', $round->id)
                ->where('def_id', $fundId)
                ->delete();
            return;
        }

        Fund::updateOrCreate([
            'def_id' => $fundId,
            'round_id' => $round->id,
        ], [
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
            'interest_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Pool $pool
     * @param bool $filter
     *
     * @return void
     * @throws MessageException
     */
    public function syncPool(Pool $pool, bool $filter): void
    {
        // Check for existing remitments.
        $payables = DB::table('payables')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('payables')))
            ->select('payables.id')
            ->distinct()
            ->pluck('id');
        if($payables->count() > 0 &&
            DB::table('remitments')->whereIn('payable_id', $payables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Check for existing deposits.
        $receivables = DB::table('receivables')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('receivables')))
            ->select('receivables.id')
            ->distinct()
            ->pluck('id');
        if($receivables->count() > 0 &&
            DB::table('deposits')->whereIn('receivable_id', $receivables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Detach the payables from their sessions.
        if($payables->count() > 0)
        {
            DB::table('payables')->whereIn('id', $payables)->update(['session_id' => null]);
        }
        // Delete the receivables.
        if($receivables->count() > 0)
        {
            DB::table('receivables')->whereIn('id', $receivables)->delete();
        }
    }

    /**
     * Called after a new session is created
     *
     * @param Session $session
     *
     * @return void
     */
    public function onNewSession(Session $session)
    {
        // Disable this session on all planned pools
        $session->pools()
            ->remitPlanned()
            ->get()
            ->each(function($pool) use($session) {
                $pool->disabled_sessions()->attach($session->id);
            });
    }

    /**
     * Called before a session is deleted
     *
     * @param Session $session
     *
     * @return void
     */
    public function onDeleteSession(Session $session)
    {
        if($session->payables()->paid()->exists() ||
            $session->receivables()->paid()->exists() ||
            $session->session_bills()->paid()->exists())
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.session.errors.delete'));
        }

        // Delete related data that may have been automatically created.
        $session->payables()->update(['session_id' => null]);
        $session->receivables()->delete();
        $session->session_bills()->delete();
        $session->disabled_pools()->detach();
    }

    /**
     * Find the prev session.
     *
     * @param Guild $guild
     * @param Session $session
     *
     * @return Session|null
     */
    private function getPrevSession(Guild $guild, Session $session): ?Session
    {
        return $guild->sessions()
            ->where('day_date', '<', $session->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Guild $guild
     * @param Session $session
     *
     * @return Session|null
     */
    private function getNextSession(Guild $guild, Session $session): ?Session
    {
        return $guild->sessions()
            ->where('day_date', '>', $session->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
    }

    /**
     * Called before a session is updated
     *
     * @param Guild $guild
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    public function onUpdateSession(Guild $guild, Session $session, array $values): void
    {
        // Check that the sessions date sorting is not modified.
        $date = Carbon::createFromFormat('Y-m-d', $values['day_date']);
        $prevSession = $this->getPrevSession($guild, $session);
        $nextSession = $this->getNextSession($guild, $session);
        if(($prevSession !== null && $prevSession->day_date->gte($date)) ||
            ($nextSession !== null && $nextSession->day_date->lte($date)))
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.session.errors.sorting'));
        }
    }
}
