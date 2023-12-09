<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $globalCategories = [
            [
                'name' => 'expense',
                'item_type' => 'disbursement',
            ],
            [
                'name' => 'support',
                'item_type' => 'disbursement',
            ],
            [
                'name' => 'reception',
                'item_type' => 'disbursement',
            ],
            [
                'name' => 'other',
                'item_type' => 'disbursement',
            ],
        ];

        if(!Category::find(0))
        {
            if(DB::connection()->getDriverName() === 'mysql')
            {
                // Force MySQL to accept 0 as primary id value.
                DB::statement("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
            }
            DB::statement("insert into categories(id,name,item_type) values(0,'','')");
        }
        foreach($globalCategories as $category)
        {
            if(!Category::where('name', $category['name'])->whereNull('tontine_id')->first())
            {
                Category::create($category);
            }
        }
    }
}
