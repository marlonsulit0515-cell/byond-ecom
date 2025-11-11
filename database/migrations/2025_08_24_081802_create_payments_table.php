<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('method'); // cash, paypal, gcash, maya
                $table->string('status')->default('pending'); // pending, paid, failed
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }   
};
