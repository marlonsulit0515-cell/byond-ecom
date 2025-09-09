<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Order extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id', 'email', 'order_number', 'status', 'total',
        'full_name', 'phone', 'country', 'province', 'city', 'barangay',
        'postal_code', 'billing_address', 'delivery_option',
        'same_as_billing', 'shipping_address',
    ];

    protected $casts = [
        'same_as_billing' => 'boolean',
        'total' => 'decimal:2'
    ];

    // Relationship with User (for registered customers)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with Payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }
}

