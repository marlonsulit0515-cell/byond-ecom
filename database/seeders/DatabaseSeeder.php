<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ShippingRatesSeeder;
use Database\Seeders\ShippingQuantityRatesSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingRatesSeeder::class,
            ShippingQuantityRatesSeeder::class,
        ]);
    }
}
