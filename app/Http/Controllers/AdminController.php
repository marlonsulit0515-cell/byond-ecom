<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    public function index()
    {
        return view('AdminPanel.products.index');
    }

    public function view_category()
    {   
        $data = Category::all();
        return view('AdminPanel.products.categories', compact('data'));
    }

    public function registered_users()
    {
        $users = User::all();
        return view('AdminPanel.support.registered-users', compact('users'));
    }

    public function add_category(Request $request)
    {   
        $request->validate([
            'category' => 'required|string|max:255|unique:categories,category_name'
        ], [
            'category.unique' => 'The category "' . $request->category . '" already exists in the system.',
            'category.required' => 'Please enter a category name before submitting.',
        ]);
        
        $data = new Category;
        $data->category_name = $request->category;
        $data->save();
        
        return redirect()->back()->with('success', 'Category added successfully!');
    }

    public function delete_category($id)
    {
        $data = Category::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

    public function manage_product()
    {
        return view('AdminPanel.products.manage');
    }

    public function add_product(Request $request)
    {
        $category = Category::all();
        return view('AdminPanel.products.create', compact('category'));
    }

    // CRUD operation products in ADMIN Panel
    public function store_product(Request $request)
    {
        // Validate inputs
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hover_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'model_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category'      => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:0',
            'stock_s'       => 'nullable|integer|min:0',
            'stock_m'       => 'nullable|integer|min:0',
            'stock_l'       => 'nullable|integer|min:0',
            'stock_xl'      => 'nullable|integer|min:0',
            'stock_2xl'     => 'nullable|integer|min:0',
            'dis_price'     => 'nullable|numeric|min:0',
        ]);

        $product = new Product();
        $product->name           = $request->name;
        $product->description    = $request->description;
        $product->price          = $request->price;
        $product->discount_price = $request->dis_price;

        // Individual size stocks
        $product->stock_s   = $request->stock_s ?? 0;
        $product->stock_m   = $request->stock_m ?? 0;
        $product->stock_l   = $request->stock_l ?? 0;
        $product->stock_xl  = $request->stock_xl ?? 0;
        $product->stock_2xl = $request->stock_2xl ?? 0;

        // Auto-calculate total quantity from individual stocks
        $product->quantity = ($request->stock_s ?? 0) + 
                           ($request->stock_m ?? 0) + 
                           ($request->stock_l ?? 0) + 
                           ($request->stock_xl ?? 0) + 
                           ($request->stock_2xl ?? 0);

        // Get the category name instead of storing the ID
        $category = Category::findOrFail($request->category);
        $product->category = $category->category_name;

        // Handle image uploads (main + optional others)
        foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
            if ($request->hasFile($field)) {
                $filename = time() . '_' . $field . '.' . $request->$field->getClientOriginalExtension();
                $request->$field->move(public_path('product'), $filename);
                $product->$field = $filename;
            }
        }

        $product->save();

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    public function show_product()
    {
        $product = Product::orderBy('created_at', 'desc')->paginate(30);
        return view('AdminPanel.products.store', compact('product'));
    }

    public function update_product($id)
    {
        $product = Product::findOrFail($id);
        $category = Category::all();
        return view('AdminPanel.products.edit', compact('product', 'category')); 
    }

    public function update_confirmation($id, Request $request)
    {
        // Validate inputs (same as store_product for consistency)
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name,' . $id,
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hover_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'model_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category'      => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:0',
            'stock_s'       => 'nullable|integer|min:0',
            'stock_m'       => 'nullable|integer|min:0',
            'stock_l'       => 'nullable|integer|min:0',
            'stock_xl'      => 'nullable|integer|min:0',
            'stock_2xl'     => 'nullable|integer|min:0',
            'dis_price'     => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);

        // Update basic fields
        $product->name           = $request->name;
        $product->description    = $request->description;
        $product->price          = $request->price;
        $product->discount_price = $request->dis_price;

        // Update stocks
        $product->stock_s   = $request->stock_s ?? 0;
        $product->stock_m   = $request->stock_m ?? 0;
        $product->stock_l   = $request->stock_l ?? 0;
        $product->stock_xl  = $request->stock_xl ?? 0;
        $product->stock_2xl = $request->stock_2xl ?? 0;

        // Auto-calculate total quantity from individual stocks
        $product->quantity = ($request->stock_s ?? 0) + 
                           ($request->stock_m ?? 0) + 
                           ($request->stock_l ?? 0) + 
                           ($request->stock_xl ?? 0) + 
                           ($request->stock_2xl ?? 0);

        // Update category (get category name like in store_product)
        if ($request->category) {
            $category = Category::findOrFail($request->category);
            $product->category = $category->category_name;
        }

        // Handle image uploads for all image types (same logic as store_product)
        foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if it exists
                if ($product->$field && file_exists(public_path('product/' . $product->$field))) {
                    unlink(public_path('product/' . $product->$field));
                }

                // Upload new image
                $filename = time() . '_' . $field . '.' . $request->$field->getClientOriginalExtension();
                $request->$field->move(public_path('product'), $filename);
                $product->$field = $filename;
            }
        }

        $product->save();

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function delete_product($id)
    {
        $product = Product::findOrFail($id);

        // Delete all related images if they exist
        foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
            if ($product->$field && file_exists(public_path('product/' . $product->$field))) {
                unlink(public_path('product/' . $product->$field));
            }
        }
        
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    public function inbox()
    {
        return view('AdminPanel.support.inbox');
    }
}