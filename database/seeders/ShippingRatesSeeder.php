<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ShippingRatesSeeder extends Seeder
{   
    
    public function run()
    {   
        DB::table('shipping_rates')->truncate();
        $philippineProvinces = [
            ['province' => 'Metro Manila (NCR)', 'price' => 80.00],
            ['province' => 'Rizal', 'price' => 100.00],
            ['province' => 'Cavite', 'price' => 100.00],
            ['province' => 'Laguna', 'price' => 100.00],
            ['province' => 'Batangas', 'price' => 100.00],
            ['province' => 'Bulacan', 'price' => 100.00],
            ['province' => 'Pampanga', 'price' => 100.00],
            ['province' => 'Bataan', 'price' => 100.00],
            ['province' => 'Tarlac', 'price' => 100.00],
            ['province' => 'Nueva Ecija', 'price' => 100.00],
            ['province' => 'Quezon', 'price' => 100.00],
            ['province' => 'Ilocos Norte', 'price' => 120.00],
            ['province' => 'Ilocos Sur', 'price' => 120.00],
            ['province' => 'La Union', 'price' => 120.00],
            ['province' => 'Pangasinan', 'price' => 120.00],
            ['province' => 'Cagayan', 'price' => 120.00],
            ['province' => 'Isabela', 'price' => 120.00],
            ['province' => 'Nueva Vizcaya', 'price' => 120.00],
            ['province' => 'Quirino', 'price' => 120.00],
            ['province' => 'Abra', 'price' => 120.00],
            ['province' => 'Benguet', 'price' => 120.00],
            ['province' => 'Ifugao', 'price' => 120.00],
            ['province' => 'Kalinga', 'price' => 120.00],
            ['province' => 'Mountain Province', 'price' => 120.00],
            ['province' => 'Albay', 'price' => 120.00],
            ['province' => 'Camarines Norte', 'price' => 120.00],
            ['province' => 'Camarines Sur', 'price' => 120.00],
            ['province' => 'Sorsogon', 'price' => 120.00],
            ['province' => 'Masbate', 'price' => 120.00],
            ['province' => 'Aurora', 'price' => 120.00],
            ['province' => 'Marinduque', 'price' => 120.00],
            ['province' => 'Oriental Mindoro', 'price' => 120.00],
            ['province' => 'Occidental Mindoro', 'price' => 120.00],
            ['province' => 'Cebu', 'price' => 140.00],
            ['province' => 'Iloilo', 'price' => 140.00],
            ['province' => 'Negros Occidental', 'price' => 140.00],
            ['province' => 'Bohol', 'price' => 140.00],
            ['province' => 'Leyte', 'price' => 140.00],
            ['province' => 'Samar', 'price' => 140.00],
            ['province' => 'Eastern Samar', 'price' => 140.00],
            ['province' => 'Northern Samar', 'price' => 140.00],
            ['province' => 'Southern Leyte', 'price' => 140.00],
            ['province' => 'Aklan', 'price' => 140.00],
            ['province' => 'Antique', 'price' => 140.00],
            ['province' => 'Capiz', 'price' => 140.00],
            ['province' => 'Guimaras', 'price' => 140.00],
            ['province' => 'Negros Oriental', 'price' => 140.00],
            ['province' => 'Siquijor', 'price' => 140.00],
            ['province' => 'Biliran', 'price' => 140.00],
            ['province' => 'Davao del Sur', 'price' => 160.00],
            ['province' => 'Davao del Norte', 'price' => 160.00],
            ['province' => 'Misamis Oriental', 'price' => 160.00],
            ['province' => 'Misamis Occidental', 'price' => 160.00],
            ['province' => 'Zamboanga del Sur', 'price' => 160.00],
            ['province' => 'Cagayan de Oro (Misamis Oriental)', 'price' => 160.00],
            ['province' => 'General Santos (South Cotabato)', 'price' => 160.00],
            ['province' => 'Agusan del Norte', 'price' => 160.00],
            ['province' => 'Agusan del Sur', 'price' => 160.00],
            ['province' => 'Bukidnon', 'price' => 160.00],
            ['province' => 'Cotabato', 'price' => 160.00],
            ['province' => 'Lanao del Norte', 'price' => 160.00],
            ['province' => 'Sultan Kudarat', 'price' => 160.00],
            ['province' => 'Surigao del Norte', 'price' => 160.00],
            ['province' => 'Surigao del Sur', 'price' => 160.00],
            ['province' => 'Zamboanga del Norte', 'price' => 160.00],
            ['province' => 'Zamboanga Sibugay', 'price' => 160.00],
            ['province' => 'Palawan', 'price' => 180.00],
            ['province' => 'Batanes', 'price' => 180.00],
            ['province' => 'Sulu', 'price' => 180.00],
            ['province' => 'Tawi-Tawi', 'price' => 180.00],
            ['province' => 'Catanduanes', 'price' => 180.00],
            ['province' => 'Dinagat Islands', 'price' => 180.00],
            ['province' => 'Basilan', 'price' => 180.00],
            ['province' => 'Lanao del Sur', 'price' => 180.00],
            ['province' => 'Maguindanao', 'price' => 180.00],
        ];

        $now = Carbon::now();
        $rates = [];

        foreach ($philippineProvinces as $provinceData) {
            $rates[] = [
                'province' => $provinceData['province'],
                'price' => $provinceData['price'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('shipping_rates')->insert($rates);
    }
}