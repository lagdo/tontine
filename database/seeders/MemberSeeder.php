<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\TontineBill;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Tontine;

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

        // Bills
        $today = now();
        foreach(Tontine::get() as $tontine)
        {
            foreach($tontine->charges()->fee()->once()->get() as $charge)
            {
                foreach($tontine->members()->get() as $member)
                {
                    $bill = new Bill();
                    $bill->charge = $charge->name;
                    $bill->amount = $charge->amount;
                    $bill->issued_at = $today;
                    $bill->save();
                    $tontineBill = new TontineBill();
                    $tontineBill->bill()->associate($bill);
                    $tontineBill->charge()->associate($charge);
                    $tontineBill->member()->associate($member);
                    $tontineBill->save();
                }
            }
        }
    }
}
