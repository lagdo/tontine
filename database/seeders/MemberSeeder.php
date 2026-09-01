<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Siak\Tontine\Model\MemberDef;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MemberDef::factory()->count(50)->create();
    }
}
