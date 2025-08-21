<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->string('hover_image')->nullable();
            $table->string('closeup_image')->nullable();
            $table->string('model_image')->nullable();
            
            // Stock fields
            $table->integer('stock_s')->nullable();
            $table->integer('stock_m')->nullable();
            $table->integer('stock_l')->nullable();
            $table->integer('stock_xl')->nullable();
            $table->integer('stock_2xl')->nullable();
            
            $table->integer('quantity')->nullable(); // optional: could be total of all sizes
            $table->decimal('price')->nullable();
            $table->decimal('discount_price')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};