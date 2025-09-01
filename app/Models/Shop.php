<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    // If you're mass assigning
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'quantity',
        'image',
        'category',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
