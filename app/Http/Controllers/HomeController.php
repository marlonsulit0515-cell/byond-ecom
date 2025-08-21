<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
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
