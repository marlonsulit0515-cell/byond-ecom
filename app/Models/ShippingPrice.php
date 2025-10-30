<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPrice extends Model
{
    // explicitly link to the table
    protected $table = 'shipping_rates';

    protected $fillable = [
        'province',
        'price',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // scope for filtering only active shipping rates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
