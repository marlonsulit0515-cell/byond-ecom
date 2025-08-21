<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; // make sure this matches your DB table name
    protected $fillable = [
        'name',
        'description',
        'category',
        'image',
        'hover_image',
        'closeup_image',
        'model_image',
        'stock_s',
        'stock_m',
        'stock_l',
        'stock_xl',
        'stock_2xl',
        'quantity',
        'price',
        'discount_price',
    ];
}
