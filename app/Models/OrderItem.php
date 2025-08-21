<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // Fillable fields
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'price', 'quantity', 'total'
    ];

    // Relationships
    public function order() {
        return $this->belongsTo(Order::class);
    }
    
    public function product() {
        return $this->belongsTo(Product::class);
    }

    // Casts for decimal fields
    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
