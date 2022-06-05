<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Siak\Tontine\Model\Country;
use Siak\Tontine\Model\Currency;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = new Country();
        $country->name = 'Cameroon';
        $country->code = 'CM';
        $country->phone = '237';
        $country->languages = ['en', 'fr'];
        $country->operators = ['mtn' => 'MTN', 'orange' => 'Orange'];
        $country->save();

        $currency = new Currency();
        $currency->code = 'XAF';
        $currency->name = 'Central African Franc';
        $currency->options = [
            'precision' => 0,
            'separator' => ['thousand' => '.', 'decimal' => ','],
            'symbol' => ['value' => 'CFA', 'swap' => false],
        ];
        $currency->save();

        $country->currencies()->attach($currency->id);
    }
}
