<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Tontine;

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

        // Populate the tontines
        $tontines = Tontine::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        // Bills
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
                'title' => 'Année 2023',
                'notes' => "Cotisations pour l'année 2023",
                'start_at' => '2023-01-01',
                'end_at' => '2023-12-31',
            ]);

            // A session for each month
            $round->sessions()->createMany([[
                'title' => 'Séance de janvier 2023',
                // 'abbrev' => 'Jan 02',
                'start_at' => '2023-01-05 16:00:00',
                'end_at' => '2023-01-05 20:00:00',
            ],[
                'title' => 'Séance de février 2023',
                // 'abbrev' => 'Fev 02',
                'start_at' => '2023-02-05 16:00:00',
                'end_at' => '2023-02-05 20:00:00',
            ],[
                'title' => 'Séance de mars 2023',
                // 'abbrev' => 'Mar 02',
                'start_at' => '2023-03-05 16:00:00',
                'end_at' => '2023-03-05 20:00:00',
            ],[
                'title' => 'Séance de avril 2023',
                // 'abbrev' => 'Avr 02',
                'start_at' => '2023-04-05 16:00:00',
                'end_at' => '2023-04-05 20:00:00',
            ],[
                'title' => 'Séance de mai 2023',
                // 'abbrev' => 'Mai 02',
                'start_at' => '2023-05-05 16:00:00',
                'end_at' => '2023-05-05 20:00:00',
            ],[
                'title' => 'Séance de juin 2023',
                // 'abbrev' => 'Jun 02',
                'start_at' => '2023-06-05 16:00:00',
                'end_at' => '2023-06-05 20:00:00',
            ],[
                'title' => 'Séance de juillet 2023',
                // 'abbrev' => 'Jul 02',
                'start_at' => '2023-07-05 16:00:00',
                'end_at' => '2023-07-05 20:00:00',
            ],[
                'title' => 'Séance de août 2023',
                // 'abbrev' => 'Aou 02',
                'start_at' => '2023-08-05 16:00:00',
                'end_at' => '2023-08-05 20:00:00',
            ],[
                'title' => 'Séance de septembre 2023',
                // 'abbrev' => 'Sep 02',
                'start_at' => '2023-09-05 16:00:00',
                'end_at' => '2023-09-05 20:00:00',
            ],[
                'title' => 'Séance de octobre 2023',
                // 'abbrev' => 'Oct 02',
                'start_at' => '2023-10-05 16:00:00',
                'end_at' => '2023-10-05 20:00:00',
            ],[
                'title' => 'Séance de novembre 2023',
                // 'abbrev' => 'Nov 02',
                'start_at' => '2023-11-05 16:00:00',
                'end_at' => '2023-11-05 20:00:00',
            ],[
                'title' => 'Séance de décembre 2023',
                // 'abbrev' => 'Dec 02',
                'start_at' => '2023-12-05 16:00:00',
                'end_at' => '2023-12-05 20:00:00',
            ]]);
        }
    }
}
