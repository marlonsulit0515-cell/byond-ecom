<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Tees',
            'Shorts',
            'Hoodies',
            'Tops',
            'Bottoms',
            'Footwears',
            'Stickers',
        ];

        $now = Carbon::now();

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'category_name' => $category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}