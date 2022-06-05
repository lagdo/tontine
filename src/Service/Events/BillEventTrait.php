<?php

namespace Siak\Tontine\Service\Events;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

use function now;

trait BillEventTrait
{
    /**
     * @param Round $round
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function roundCreated(Tontine $tontine, Round $round)
    {
        $today = now();
        $tontine->charges()->round()->get()->each(function($charge) use($round, $today) {
            $charge->bills()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => $today,
                'round_id' => $round->id,
            ]);
        });
    }

    /**
     * @param Session $session
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function sessionCreated(Tontine $tontine, Session $session)
    {
        $today = now();
        $tontine->charges()->session()->get()->each(function($charge) use($session, $today) {
            $charge->bills()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => $today,
                'session_id' => $session->id,
            ]);
        });
    }

    /**
     * @param Charge $charge
     * @param Round $round
     *
     * @return void
     */
    protected function chargeCreated(Charge $charge, Round $round)
    {
        if($charge->is_fine)
        {
            return;
        }
        $today = now();
        if($charge->period_once)
        {
            $charge->bills()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => $today,
            ]);
            return;
        }
        if($charge->period_round)
        {
            $charge->bills()->create([
                'name' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => $today,
                'round_id' => $round->id,
            ]);
            return;
        }
        // if($charge->period_session)
        $charge->bills()->createMany($round->sessions->map(function($session) use($charge, $today) {
            return [
                'name' => $charge->name,
                'amount' => $charge->amount,
                'issued_at' => $today,
                'session_id' => $session->id,
            ];
        }));
    }
}
