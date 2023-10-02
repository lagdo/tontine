<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoolPropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pool properties
        $properties = [
            'libre' => [
                'deposit' => [
                    'fixed' => false,
                ],
                'remit' => [
                    'fixed' => true,
                    'planned' => true,
                    'auction' => false,
                    'lendable' => false,
                ],
            ],
            'mutual' => [
                'deposit' => [
                    'fixed' => true,
                ],
                'remit' => [
                    'fixed' => true,
                    'planned' => true,
                    'auction' => false,
                    'lendable' => false,
                ],
            ],
            'financial' => [
                'deposit' => [
                    'fixed' => true,
                ],
                'remit' => [
                    'fixed' => true,
                    'planned' => true,
                    'auction' => true,
                    'lendable' => false,
                ],
            ],
        ];

        DB::table('pools')->join('rounds', 'pools.round_id', '=', 'rounds.id')
            ->join('tontines', 'rounds.tontine_id', '=', 'tontines.id')
            ->where('tontines.type', 'l')
            ->update(['properties' => json_encode($properties['libre'])]);
        DB::table('pools')->join('rounds', 'pools.round_id', '=', 'rounds.id')
            ->join('tontines', 'rounds.tontine_id', '=', 'tontines.id')
            ->where('tontines.type', 'm')
            ->update(['properties' => json_encode($properties['mutual'])]);
        DB::table('pools')->join('rounds', 'pools.round_id', '=', 'rounds.id')
            ->join('tontines', 'rounds.tontine_id', '=', 'tontines.id')
            ->where('tontines.type', 'f')
            ->update(['properties' => json_encode($properties['financial'])]);
    }
}
