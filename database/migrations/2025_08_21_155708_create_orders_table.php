<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // null if guest
            $table->string('email')->nullable();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending'); // pending, processing, shipped, completed, cancelled
            $table->decimal('total', 10, 2);
            
            // Billing
            $table->string('full_name');
            $table->string('phone');
            $table->string('country')->default('Philippines');
            $table->string('province');
            $table->string('city');
            $table->string('barangay');
            $table->string('postal_code');
            $table->string('billing_address');

            // Shipping
            $table->string('delivery_option')->default('ship'); // ship | pickup
            $table->boolean('same_as_billing')->default(true);
            $table->string('shipping_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
