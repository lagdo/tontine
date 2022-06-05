<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Country;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\Fund;

class TontineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // The user
        $user = User::first();
        // The default country and currency
        $currency = Currency::where('code', 'XAF')->first();
        $country = Country::where('code', 'CM')->first();

        // Populate the tontines
        $tontines = Tontine::factory()->count(2)->create([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'currency_id' => $currency->id,
        ]);

        // Bill Reasons
        foreach($tontines as $tontine)
        {
            $tontine->charges()->createMany([[
                'name' => "Amende pour retard",
                'type' => 1,
                'period' => Charge::PERIOD_NONE,
                'amount' => 500,
            ],[
                'name' => "Amende pour désordre",
                'type' => 1,
                'period' => Charge::PERIOD_NONE,
                'amount' => 500,
            ],[
                'name' => "Frais de dossier",
                'type' => 0,
                'period' => Charge::PERIOD_ONCE,
                'amount' => 2000,
            ],[
                'name' => "Frais d'inscription",
                'type' => 0,
                'period' => Charge::PERIOD_ROUND,
                'amount' => 8000,
            ],[
                'name' => "Participation à la réception",
                'type' => 0,
                'period' => Charge::PERIOD_SESSION,
                'amount' => 1000,
            ]]);
            $round = $tontine->rounds()->create([
                'title' => 'Année 2022',
                'notes' => "Cotisations pour l'année 2022",
                'start_at' => '2022-01-01',
                'end_at' => '2022-12-31',
            ]);

            // A session for each month
            $round->sessions()->createMany([[
                'title' => 'Séance de janvier 2022',
                // 'abbrev' => 'Jan 02',
                'start_at' => '2022-01-05 16:00:00',
                'end_at' => '2022-01-05 20:00:00',
            ],[
                'title' => 'Séance de février 2022',
                // 'abbrev' => 'Fev 02',
                'start_at' => '2022-02-05 16:00:00',
                'end_at' => '2022-02-05 20:00:00',
            ],[
                'title' => 'Séance de mars 2022',
                // 'abbrev' => 'Mar 02',
                'start_at' => '2022-03-05 16:00:00',
                'end_at' => '2022-03-05 20:00:00',
            ],[
                'title' => 'Séance de avril 2022',
                // 'abbrev' => 'Avr 02',
                'start_at' => '2022-04-05 16:00:00',
                'end_at' => '2022-04-05 20:00:00',
            ],[
                'title' => 'Séance de mai 2022',
                // 'abbrev' => 'Mai 02',
                'start_at' => '2022-05-05 16:00:00',
                'end_at' => '2022-05-05 20:00:00',
            ],[
                'title' => 'Séance de juin 2022',
                // 'abbrev' => 'Jun 02',
                'start_at' => '2022-06-05 16:00:00',
                'end_at' => '2022-06-05 20:00:00',
            ],[
                'title' => 'Séance de juillet 2022',
                // 'abbrev' => 'Jul 02',
                'start_at' => '2022-07-05 16:00:00',
                'end_at' => '2022-07-05 20:00:00',
            ],[
                'title' => 'Séance de août 2022',
                // 'abbrev' => 'Aou 02',
                'start_at' => '2022-08-05 16:00:00',
                'end_at' => '2022-08-05 20:00:00',
            ],[
                'title' => 'Séance de septembre 2022',
                // 'abbrev' => 'Sep 02',
                'start_at' => '2022-09-05 16:00:00',
                'end_at' => '2022-09-05 20:00:00',
            ],[
                'title' => 'Séance de octobre 2022',
                // 'abbrev' => 'Oct 02',
                'start_at' => '2022-10-05 16:00:00',
                'end_at' => '2022-10-05 20:00:00',
            ],[
                'title' => 'Séance de novembre 2022',
                // 'abbrev' => 'Nov 02',
                'start_at' => '2022-11-05 16:00:00',
                'end_at' => '2022-11-05 20:00:00',
            ],[
                'title' => 'Séance de décembre 2022',
                // 'abbrev' => 'Dec 02',
                'start_at' => '2022-12-05 16:00:00',
                'end_at' => '2022-12-05 20:00:00',
            ]]);

            // A few funds
            Fund::unguard();
            $sessionIds = $round->sessions()->pluck('id')->all();
            $round->funds()->createMany([[
                'title' => "Liste de 5000",
                'amount' => 5000,
                'session_ids' => $sessionIds,
            ],[
                'title' => "Liste de 10000",
                'amount' => 10000,
                'session_ids' => $sessionIds,
            ],[
                'title' => "Liste de 15000",
                'amount' => 15000,
                'session_ids' => $sessionIds,
            ],[
                'title' => "Liste de 20000",
                'amount' => 20000,
                'session_ids' => $sessionIds,
            ]]);
            Fund::reguard();

            // Bills
            foreach($tontine->charges()->fee()->get() as $charge)
            {
                $bill = [
                    'name' => $charge->name,
                    'amount' => $charge->amount,
                    'issued_at' => now(),
                ];
                if($charge->period === Charge::PERIOD_ONCE)
                {
                    $charge->bills()->create($bill);
                    continue;
                }
                if($charge->period === Charge::PERIOD_ROUND)
                {
                    $bill['round_id'] = $round->id;
                    $charge->bills()->create($bill);
                    continue;
                }
                // $charge->period === Charge::PERIOD_SESSION
                foreach($round->sessions as $session)
                {
                    $bill['session_id'] = $session->id;
                    $charge->bills()->create($bill);
                }
            }
        }
    }
}
