<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingQuantityRate extends Model
{
    protected $fillable = [
        'quantity_from',
        'quantity_to',
        'fixed_price',
        'is_active',
    ];

    protected $casts = [
        'quantity_from' => 'integer',
        'quantity_to' => 'integer',
        'fixed_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Find the appropriate fixed rate for a given quantity
     */
    public static function findRateForQuantity($quantity)
    {
        return self::where('is_active', true)
            ->where('quantity_from', '<=', $quantity)
            ->where('quantity_to', '>=', $quantity)
            ->orderBy('quantity_from', 'desc')
            ->first();
    }
}
