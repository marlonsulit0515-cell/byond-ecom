<?php

namespace App\Http\Controllers;
use \App\Models\Product;
use Illuminate\Support\Facades\DB;
use \App\Models\Category;

use Illuminate\Http\Request;

class HomeController extends Controller
    {   
    
         

    //Filter for the shop page
    public function shop_menu() {
        $query = Product::query();
        
        // Sorting
        switch(request('sort')) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderByRaw("COALESCE(discount_price, price) ASC");
                break;
            case 'price_desc':
                $query->orderByRaw("COALESCE(discount_price, price) DESC");
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        // Price filter
        if (request('price_from') && request('price_to') && is_numeric(request('price_from')) && is_numeric(request('price_to'))) {
            $query->whereBetween(DB::raw("COALESCE(discount_price, price)"), [request('price_from'), request('price_to')]);
        } elseif (request('price_from') && is_numeric(request('price_from'))) {
            $query->whereRaw("COALESCE(discount_price, price) >= ?", [request('price_from')]);
        } elseif (request('price_to') && is_numeric(request('price_to'))) {
            $query->whereRaw("COALESCE(discount_price, price) <= ?", [request('price_to')]);
        }

        // Availability filter
        if (request('availability')) {
            if (request('availability') == 'in_stock') {
                $query->where(function($q) {
                    $q->where('stock_s', '>', 0)
                    ->orWhere('stock_m', '>', 0)
                    ->orWhere('stock_l', '>', 0)
                    ->orWhere('stock_xl', '>', 0)
                    ->orWhere('stock_2xl', '>', 0);
                });
            } elseif (request('availability') == 'out_of_stock') {
                $query->whereRaw("
                    COALESCE(stock_s, 0) <= 0 AND
                    COALESCE(stock_m, 0) <= 0 AND
                    COALESCE(stock_l, 0) <= 0 AND
                    COALESCE(stock_xl, 0) <= 0 AND
                    COALESCE(stock_2xl, 0) <= 0
                ");
            }
        }

        // Size filter
        if (request('size')) {
            $sizeColumn = 'stock_' . request('size');
            $query->where($sizeColumn, '>', 0);
        }

        // Pagination with persistent filters
        $product = $query->paginate(15)->appends(request()->query());

        // Handle AJAX requests for "Load More"
        if (request()->ajax()) {
            return response()->json([
                'html' => view('shop.shop-page', ['product' => $product])->render(),
                'hasMore' => $product->hasMorePages(),
                'nextPage' => $product->currentPage() + 1
            ]);
        }

        // Regular page load
        return view("shop.shop-page", compact('product'));
    }


    public function category_dropdown($category)
    {
        $product = Product::where('category', $category)->latest()->paginate(12);

        return view('shop.shop-page', compact('product', 'category'));
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
