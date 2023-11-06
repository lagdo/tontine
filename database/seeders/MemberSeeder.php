<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Siak\Tontine\Model\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Member::factory()->count(50)->create();
    }
}
