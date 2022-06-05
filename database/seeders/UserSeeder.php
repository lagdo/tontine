<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Siak\Tontine\Model\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->password = Hash::make('password');
        $user->email = 'admin@company.com';
        $user->name = 'Admin';
        $user->save();
    }
}
