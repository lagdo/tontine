<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Guild;

class GuildSeeder extends Seeder
{
    /**
     * @param Guild $guild
     *
     * @return void
     */
    private function createFunds(Guild $guild): void
    {
        $guild->funds()->createMany([[
            'title' => '', // The mandatory default fund
        ],[
            'title' => 'Banque scolaire',
        ],[
            'title' => 'Banque annuelle',
        ]]);
    }

    /**
     * @param Guild $guild
     *
     * @return void
     */
    private function createPools(Guild $guild): void
    {
        $guild->pools()->createMany([[
            'title' => 'Tontine avec montant libre',
            'amount' => 0,
            'properties' => [
                'deposit' => [
                    'fixed' => false,
                    'lendable' => false,
                ],
                'remit' => [
                    'planned' => true,
                    'auction' => false,
                ],
            ],
        ],[
            'title' => 'Tontine avec remise planifiée',
            'amount' => 10000,
            'properties' => [
                'deposit' => [
                    'fixed' => true,
                    'lendable' => false,
                ],
                'remit' => [
                    'planned' => true,
                    'auction' => false,
                ],
            ],
        ],[
            'title' => 'Tontine avec prêt',
            'amount' => 15000,
            'properties' => [
                'deposit' => [
                    'fixed' => true,
                    'lendable' => true,
                ],
                'remit' => [
                    'planned' => false,
                    'auction' => false,
                ],
            ],
        ],[
            'title' => 'Tontine avec enchère et prêt',
            'amount' => 20000,
            'properties' => [
                'deposit' => [
                    'fixed' => true,
                    'lendable' => true,
                ],
                'remit' => [
                    'planned' => false,
                    'auction' => true,
                ],
            ],
        ],[
            'title' => 'Tontine avec enchère',
            'amount' => 25000,
            'properties' => [
                'deposit' => [
                    'fixed' => true,
                    'lendable' => false,
                ],
                'remit' => [
                    'planned' => false,
                    'auction' => true,
                ],
            ],
        ]]);
    }

    /**
     * @param Guild $guild
     *
     * @return void
     */
    private function createCharges(Guild $guild): void
    {
        $guild->charges()->createMany([[
            'name' => "Amende pour retard",
            'type' => Charge::TYPE_FINE,
            'period' => Charge::PERIOD_NONE,
            'amount' => 500,
            'lendable' => true,
        ],[
            'name' => "Amende pour désordre",
            'type' => Charge::TYPE_FINE,
            'period' => Charge::PERIOD_NONE,
            'amount' => 0,
            'lendable' => true,
        ],[
            'name' => "Contribution de solidarité",
            'type' => Charge::TYPE_FEE,
            'period' => Charge::PERIOD_NONE,
            'amount' => 0,
            'lendable' => false,
        ],[
            'name' => "Frais de dossier",
            'type' => Charge::TYPE_FEE,
            'period' => Charge::PERIOD_ONCE,
            'amount' => 2000,
            'lendable' => false,
        ],[
            'name' => "Frais d'inscription",
            'type' => Charge::TYPE_FEE,
            'period' => Charge::PERIOD_ROUND,
            'amount' => 8000,
            'lendable' => false,
        ],[
            'name' => "Participation à la réception",
            'type' => Charge::TYPE_FEE,
            'period' => Charge::PERIOD_SESSION,
            'amount' => 1000,
            'lendable' => false,
        ]]);
    }

    /**
     * @param Guild $guild
     *
     * @return void
     */
    private function createSessions(Guild $guild): void
    {
        $round = $guild->rounds()->create([
            'title' => 'Année 2025',
            'notes' => "Cotisations pour l'année 2025",
            'status' => 1,
        ]);

        // A session for each month
        $round->sessions()->createMany([[
            'title' => 'Séance de janvier 2025',
            // 'abbrev' => 'Jan 02',
            'day_date' => '2025-01-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de février 2025',
            // 'abbrev' => 'Fev 02',
            'day_date' => '2025-02-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de mars 2025',
            // 'abbrev' => 'Mar 02',
            'day_date' => '2025-03-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de avril 2025',
            // 'abbrev' => 'Avr 02',
            'day_date' => '2025-04-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de mai 2025',
            // 'abbrev' => 'Mai 02',
            'day_date' => '2025-05-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de juin 2025',
            // 'abbrev' => 'Jun 02',
            'day_date' => '2025-06-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de juillet 2025',
            // 'abbrev' => 'Jul 02',
            'day_date' => '2025-07-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de août 2025',
            // 'abbrev' => 'Aou 02',
            'day_date' => '2025-08-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de septembre 2025',
            // 'abbrev' => 'Sep 02',
            'day_date' => '2025-09-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de octobre 2025',
            // 'abbrev' => 'Oct 02',
            'day_date' => '2025-10-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de novembre 2025',
            // 'abbrev' => 'Nov 02',
            'day_date' => '2025-11-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ],[
            'title' => 'Séance de décembre 2025',
            // 'abbrev' => 'Dec 02',
            'day_date' => '2025-12-05',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
        ]]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // The user
        $user = User::first();

        // Populate the guilds
        $guilds = Guild::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        foreach($guilds as $guild)
        {
            $this->createFunds($guild);
            $this->createPools($guild);
            $this->createCharges($guild);
            $this->createSessions($guild);
        }
    }
}
