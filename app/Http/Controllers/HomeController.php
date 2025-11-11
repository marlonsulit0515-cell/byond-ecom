<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Home page
    public function homepage_products()
    {
        $categories = Category::all(); // <-- needed for header
        $recentProducts = Product::latest()->take(15)->get();
        $salesProducts = Product::whereNotNull('discount_price')
                                ->where('discount_price', '>', 0)
                                ->latest()
                                ->take(15)
                                ->get();
        $teesProducts = Product::where('category', 'Tees')
                               ->latest()
                               ->take(15)
                               ->get();

        return view('home', compact('recentProducts', 'salesProducts', 'teesProducts', 'categories'));
    }
    public function navigation_categories()
    {
        $categories = Category::all();
        return view('layouts.navigation', compact($categories));
    }

    // Apply filters
    private function applyFilters($query)
    {
        switch (request('sort')) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'price_asc': $query->orderByRaw("COALESCE(discount_price, price) ASC"); break;
            case 'price_desc': $query->orderByRaw("COALESCE(discount_price, price) DESC"); break;
            default: $query->latest(); break;
        }

        if (request('price_from') && is_numeric(request('price_from'))) {
            $query->whereRaw("COALESCE(discount_price, price) >= ?", [request('price_from')]);
        }
        if (request('price_to') && is_numeric(request('price_to'))) {
            $query->whereRaw("COALESCE(discount_price, price) <= ?", [request('price_to')]);
        }

        if (request('availability')) {
            if (request('availability') === 'in_stock') {
                $query->where(function($q){
                    $q->where('stock_s', '>', 0)
                      ->orWhere('stock_m', '>', 0)
                      ->orWhere('stock_l', '>', 0)
                      ->orWhere('stock_xl', '>', 0)
                      ->orWhere('stock_2xl', '>', 0);
                });
            } elseif (request('availability') === 'out_of_stock') {
                $query->whereRaw("
                    COALESCE(stock_s, 0) <= 0 AND
                    COALESCE(stock_m, 0) <= 0 AND
                    COALESCE(stock_l, 0) <= 0 AND
                    COALESCE(stock_xl, 0) <= 0 AND
                    COALESCE(stock_2xl, 0) <= 0
                ");
            }
        }

        if (request('size')) {
            $sizeColumn = 'stock_' . request('size');
            $query->where($sizeColumn, '>', 0);
        }

        return $query;
    }

    // All products
    public function shop_menu()
    {
        $categories = Category::all();
        $query = $this->applyFilters(Product::query());
        $product = $query->paginate(15)->appends(request()->query());

        if (request()->ajax()) {
            return response()->json([
                'html' => view('shop.shop-page', compact('product', 'categories'))->render(),
                'hasMore' => $product->hasMorePages(),
                'nextPage' => $product->currentPage() + 1
            ]);
        }

        return view('shop.shop-page', compact('product', 'categories'));
    }

    // Products by category
    public function category_dropdown($category)
    {
        $categories = Category::all();
        $query = $this->applyFilters(Product::where('category', $category));
        $product = $query->paginate(15)->appends(request()->query());

        if (request()->ajax()) {
            return response()->json([
                'html' => view('shop.shop-page', compact('product', 'category', 'categories'))->render(),
                'hasMore' => $product->hasMorePages(),
                'nextPage' => $product->currentPage() + 1
            ]);
        }

        return view('shop.shop-page', compact('product', 'category', 'categories'));
    }
}
