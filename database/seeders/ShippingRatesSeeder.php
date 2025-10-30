<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingRatesSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Abra', 'Agusan del Norte', 'Agusan del Sur', 'Aklan', 'Albay', 'Antique', 'Apayao', 'Aurora',
            'Basilan', 'Bataan', 'Batanes', 'Batangas', 'Benguet', 'Biliran', 'Bohol', 'Bukidnon',
            'Bulacan', 'Cagayan', 'Camarines Norte', 'Camarines Sur', 'Camiguin', 'Capiz', 'Catanduanes',
            'Cavite', 'Cebu', 'Cotabato', 'Davao de Oro', 'Davao del Norte', 'Davao del Sur',
            'Davao Occidental', 'Davao Oriental', 'Dinagat Islands', 'Eastern Samar', 'Guimaras',
            'Ifugao', 'Ilocos Norte', 'Ilocos Sur', 'Iloilo', 'Isabela', 'Kalinga', 'La Union',
            'Laguna', 'Lanao del Norte', 'Lanao del Sur', 'Leyte', 'Maguindanao del Norte', 
            'Maguindanao del Sur', 'Marinduque', 'Masbate', 'Misamis Occidental', 'Misamis Oriental',
            'Mountain Province', 'Negros Occidental', 'Negros Oriental', 'Northern Samar', 'Nueva Ecija',
            'Nueva Vizcaya', 'Occidental Mindoro', 'Oriental Mindoro', 'Palawan', 'Pampanga', 'Pangasinan',
            'Quezon', 'Quirino', 'Rizal', 'Romblon', 'Samar', 'Sarangani', 'Siquijor', 'Sorsogon',
            'South Cotabato', 'Southern Leyte', 'Sultan Kudarat', 'Sulu', 'Surigao del Norte',
            'Surigao del Sur', 'Tarlac', 'Tawi-Tawi', 'Zambales', 'Zamboanga del Norte',
            'Zamboanga del Sur', 'Zamboanga Sibugay'
        ];

        // Sample price logic: vary slightly by region grouping
        $rates = [];
        foreach ($provinces as $province) {
            $price = match (true) {
                in_array($province, ['Metro Manila', 'Cavite', 'Laguna', 'Bulacan', 'Rizal']) => 100.00,
                str_contains(strtolower($province), 'mindoro') => 140.00,
                str_contains(strtolower($province), 'mindanao') => 200.00,
                str_contains(strtolower($province), 'cebu') => 160.00,
                default => fake()->randomFloat(2, 120, 200),
            };

            $rates[] = [
                'province' => $province,
                'price' => $price,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('shipping_rates')->insert($rates);
    }
}
