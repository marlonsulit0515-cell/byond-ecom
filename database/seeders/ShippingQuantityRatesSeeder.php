<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ShippingQuantityRatesSeeder extends Seeder
{
    public function run()
    {
        DB::table('shipping_quantity_rates')->truncate();
        $quantityRates = [
            ['quantity_from' => 1, 'quantity_to' => 3, 'fixed_price' => 40.00],
            ['quantity_from' => 4, 'quantity_to' => 7, 'fixed_price' => 60.00],
            ['quantity_from' => 8, 'quantity_to' => 12, 'fixed_price' => 80.00],
            ['quantity_from' => 13, 'quantity_to' => 20, 'fixed_price' => 100.00],
            ['quantity_from' => 21, 'quantity_to' => 9999, 'fixed_price' => 150.00],
        ];

        $now = Carbon::now();

        foreach ($quantityRates as $rateData) {
            DB::table('shipping_quantity_rates')->insert([
                'quantity_from' => $rateData['quantity_from'],
                'quantity_to' => $rateData['quantity_to'],
                'fixed_price' => $rateData['fixed_price'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}