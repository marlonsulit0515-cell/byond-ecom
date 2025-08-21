<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class UserController extends Controller
{
    public function index()
    {
        $product = Product::all(); // fetch all products
        return view('home', compact('product'));
    }
    public function view_shop()
    {
        return view('shop.shop-page'); // Assuming you have a view named 'shop.shop-page'
    }
}
