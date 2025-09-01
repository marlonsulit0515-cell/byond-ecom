<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [];

        // 20 Tees
        for ($i = 1; $i <= 20; $i++) {
            $products[] = [
                'name' => "Tee Shirt $i",
                'description' => "Comfortable and stylish Tee number $i.",
                'category' => "Tees",
                'image' => "https://picsum.photos/seed/tee{$i}/600/600",
                'hover_image' => "https://picsum.photos/seed/tee{$i}h/600/600",
                'closeup_image' => "https://picsum.photos/seed/tee{$i}c/600/600",
                'model_image' => "https://picsum.photos/seed/tee{$i}m/600/600",
                'stock_s' => rand(5, 20),
                'stock_m' => rand(5, 20),
                'stock_l' => rand(5, 20),
                'stock_xl' => rand(5, 20),
                'stock_2xl' => rand(5, 20),
                'quantity' => null,
                'price' => rand(300, 600),
                'discount_price' => rand(0, 200),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 20 Hoodies
        for ($i = 1; $i <= 20; $i++) {
            $products[] = [
                'name' => "Hoodie $i",
                'description' => "Warm and cozy Hoodie number $i.",
                'category' => "Hoodies",
                'image' => "https://picsum.photos/seed/hoodie{$i}/600/600",
                'hover_image' => "https://picsum.photos/seed/hoodie{$i}h/600/600",
                'closeup_image' => "https://picsum.photos/seed/hoodie{$i}c/600/600",
                'model_image' => "https://picsum.photos/seed/hoodie{$i}m/600/600",
                'stock_s' => rand(5, 15),
                'stock_m' => rand(5, 15),
                'stock_l' => rand(5, 15),
                'stock_xl' => rand(5, 15),
                'stock_2xl' => rand(5, 15),
                'quantity' => null,
                'price' => rand(800, 1500),
                'discount_price' => rand(0, 400),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 10 New Arrivals
        for ($i = 1; $i <= 10; $i++) {
            $products[] = [
                'name' => "New Arrival $i",
                'description' => "Exclusive new arrival product $i.",
                'category' => "New Arrival",
                'image' => "https://picsum.photos/seed/new{$i}/600/600",
                'hover_image' => "https://picsum.photos/seed/new{$i}h/600/600",
                'closeup_image' => "https://picsum.photos/seed/new{$i}c/600/600",
                'model_image' => "https://picsum.photos/seed/new{$i}m/600/600",
                'stock_s' => rand(5, 15),
                'stock_m' => rand(5, 15),
                'stock_l' => rand(5, 15),
                'stock_xl' => rand(5, 15),
                'stock_2xl' => rand(5, 15),
                'quantity' => null,
                'price' => rand(500, 1200),
                'discount_price' => rand(0, 300),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 10 Sale Items
        for ($i = 1; $i <= 10; $i++) {
            $products[] = [
                'name' => "Sale Product $i",
                'description' => "Discounted sale item $i.",
                'category' => "Sale",
                'image' => "https://picsum.photos/seed/sale{$i}/600/600",
                'hover_image' => "https://picsum.photos/seed/sale{$i}h/600/600",
                'closeup_image' => "https://picsum.photos/seed/sale{$i}c/600/600",
                'model_image' => "https://picsum.photos/seed/sale{$i}m/600/600",
                'stock_s' => rand(2, 10),
                'stock_m' => rand(2, 10),
                'stock_l' => rand(2, 10),
                'stock_xl' => rand(2, 10),
                'stock_2xl' => rand(2, 10),
                'quantity' => null,
                'price' => rand(400, 1000),
                'discount_price' => rand(100, 300),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('products')->insert($products);
    }
}
