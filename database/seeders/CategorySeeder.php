<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        foreach($globalCategories as $category)
        {
            if(!Category::where('name', $category['name'])->first())
            {
                Category::create($category);
            }
        }
    }
}
