<?php

namespace App\Http\Controllers;
use \App\Models\Product;

use Illuminate\Http\Request;

class HomeController extends Controller
{   
    public function shop_menu()
    {   
         $product=Product::all();
        return view("shop.shop-page",  compact('product')) ;

    }
    public function blog_content_one()
    {
        return view("blogs.content1");
    }
    public function blog_content_two()
    {
        return view("blogs.content2");
    }
    public function blog_content_three()
    {
        return view("blogs.content3");
    }
    public function blog_content_four()
    {
        return view("blogs.content4");
    }
}
