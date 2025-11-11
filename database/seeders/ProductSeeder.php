<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Tees', 'Shorts', 'Hoodies', 'Tops', 'Bottoms', 'Footwears', 'Stickers'];
        $imageFiles = [
            '1756012775_image.jpg',
            '1756012775_model.png',
            '1756169238_closeup_image.png',
            '1756169238_hover_image.jpg',
            '1756169238_model_image.png',
            '1756169312_closeup_image.jpg',
            '1756169312_image.png',
            '1756169312_model_image.png',
            '1756361490_closeup_image.png',
            '1756361490_hover_image.jpg',
            '1756361490_image.png',
            '1756363154_model_image.png',
            '1757242836_closeup_image.png',
            '1757242836_image.png',
            '1757242836_model_image.png',
            '1757424803_closeup_image.png',
            '1757424803_hover_image.jpg',
            '1757424803_image.jpg',
            '1757424803_model_image.png',
            '1760658963_closeup_image.jpeg',
            '1760658963_hover_image.jpeg',
            '1760658963_image.jpg',
            '1760658963_model_image.jpeg',
            '1760861005_closeup_image.jpg',
            '1760861005_hover_image.jpg',
            '1760861005_image.jpg',
            '1760861005_model_image.jpg',
            'Byond-Rocket.jpg',
            'Byond-Stocked-Times.png',
            'Byond-Stoke_Times.png',
            'hoodie-blackimage.jpg',
            'shirt-blackimage.jpg',
        ];

        $now = Carbon::now();
        $products = [];
        $sizes = ['s', 'm', 'l', 'xl', '2xl'];
        $numProductsPerCategory = 20;

        foreach ($categories as $category) {
            for ($i = 1; $i <= $numProductsPerCategory; $i++) {
                $name = "{$category} Product " . Str::padLeft($i, 2, '0');
                $baseImage = $imageFiles[array_rand($imageFiles)];

                // Simple logic to find related images
                $nameBase = pathinfo($baseImage, PATHINFO_FILENAME);
                $namePrefix = explode('_', $nameBase)[0];

                $relatedImages = array_filter($imageFiles, function($file) use ($namePrefix, $baseImage) {
                    return $file !== $baseImage && Str::startsWith($file, $namePrefix);
                });
                
                $hoverImage = head(array_filter($relatedImages, fn($file) => Str::contains($file, 'hover_image')));
                $closeupImage = head(array_filter($relatedImages, fn($file) => Str::contains($file, 'closeup_image')));
                $modelImage = head(array_filter($relatedImages, fn($file) => Str::contains($file, 'model_image')));
                
                // Fallback for related images
                $hoverImage = $hoverImage ?: $baseImage;
                $closeupImage = $closeupImage ?: $baseImage;
                $modelImage = $modelImage ?: $baseImage;
                
                // Set stock for each size to a random value between 10 and 50
                $stockS = rand(10, 50);
                $stockM = rand(10, 50);
                $stockL = rand(10, 50);
                $stockXL = rand(10, 50);
                $stock2XL = rand(10, 50);
                $totalQuantity = $stockS + $stockM + $stockL + $stockXL + $stock2XL;

                $products[] = [
                    'name' => $name,
                    'description' => "This is the description for {$name}. It belongs to the {$category} collection and is available in multiple sizes.",
                    'category' => $category,
                    'image' => $baseImage,
                    'hover_image' => $hoverImage,
                    'closeup_image' => $closeupImage,
                    'model_image' => $modelImage,
                    'stock_s' => $stockS,
                    'stock_m' => $stockM,
                    'stock_l' => $stockL,
                    'stock_xl' => $stockXL,
                    'stock_2xl' => $stock2XL,
                    'quantity' => $totalQuantity,
                    'price' => rand(599, 2999) + (rand(0, 99) / 100), // Random price from 599.00 to 2999.99
                    'discount_price' => null, // As requested, no discount price
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('products')->insert($products);
    }
}