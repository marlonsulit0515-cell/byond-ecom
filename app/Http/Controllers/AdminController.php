<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
class AdminController extends Controller
{
    public function index()
    {
        return view('AdminPanel.products.index'); // Assuming you have a view named 'admin.dashboard'
    }

    public function view_category()
    {   
        $data=Category::all();
        return view('AdminPanel.products.categories', compact('data')); // Assuming you have a view for categories
    }
    public function add_category(Request $request)
    {   
       $request->validate([
        'category' => 'required|string|max:255|unique:categories,category_name'
    ], [
        'category.unique' => 'The category "' . $request->category . '" already exists in the system.',
        'category.required' => 'Please enter a category name before submitting.',
    ]);
        $data=new Category;
        $data-> category_name=$request->category;

        $data->save(); // Save the category to the database
        return redirect()->back()->with('success', 'Category added successfully!'); // Redirect back
    }
   public function delete_category($id)
    {
        $data = Category::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

    public function manage_product()
        {
            return view('AdminPanel.products.manage'); // No form here
        }
    public function add_product(Request $request)
        {
            $category = Category::all();
            return view('AdminPanel.products.create', compact('category'));
        }

    //CRUD operation products in ADMIN Panel
    public function store_product(Request $request)
        {
            // Validate inputs
            $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'hover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'model_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'category'    => 'required|exists:categories,id',
                'price'       => 'required|numeric|min:1',
                'stock_s'     => 'nullable|integer|min:0',
                'stock_m'     => 'nullable|integer|min:0',
                'stock_l'     => 'nullable|integer|min:0',
                'stock_xl'    => 'nullable|integer|min:0',
                'stock_2xl'   => 'nullable|integer|min:0',
                'quantity'    => 'nullable|integer|min:0',
                'dis_price'   => 'nullable|numeric|min:0',
            ]);

            $product = new Product();
            $product->name           = $request->name;
            $product->description    = $request->description;
            $product->price          = $request->price;
            $product->discount_price = $request->dis_price;

            // Stocks
            $product->stock_s   = $request->stock_s;
            $product->stock_m   = $request->stock_m;
            $product->stock_l   = $request->stock_l;
            $product->stock_xl  = $request->stock_xl;
            $product->stock_2xl = $request->stock_2xl;
            $product->quantity  = $request->quantity;

            // Get the category name instead of storing the ID
            $category = Category::findOrFail($request->category);
            $product->category = $category->category_name; // Store the name instead of ID

            // Handle image uploads (main + optional others)
            foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
                if ($request->hasFile($field)) {
                    $filename = time().'_'.$field.'.'.$request->$field->getClientOriginalExtension();
                    $request->$field->move(public_path('product'), $filename);
                    $product->$field = $filename;
                }
            }

            $product->save(); // Save to DB

            return redirect()->back()->with('success', 'Product added successfully!');
        }

        public function show_product()
        {
            $product=product::all(); // Fetch all products
            return view('AdminPanel.products.store', compact('product')); // Assuming you have a view for showing products
        }
        public function update_product($id)
        {
            $product = Product::findOrFail($id); // Use findOrFail for better error handling
            $category = Category::all(); // Fetch all categories
            return view('AdminPanel.products.edit', compact('product', 'category')); 
        }

        public function update_confirmation($id, Request $request)
        {
            // Validate inputs (same as store_product for consistency)
            $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'hover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'model_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'category'    => 'required|exists:categories,id',
                'price'       => 'required|numeric|min:1',
                'stock_s'     => 'nullable|integer|min:0',
                'stock_m'     => 'nullable|integer|min:0',
                'stock_l'     => 'nullable|integer|min:0',
                'stock_xl'    => 'nullable|integer|min:0',
                'stock_2xl'   => 'nullable|integer|min:0',
                'quantity'    => 'nullable|integer|min:0',
                'dis_price'   => 'nullable|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);

            // Update basic fields
            $product->name           = $request->name;
            $product->description    = $request->description;
            $product->price          = $request->price;
            $product->discount_price = $request->dis_price;

            // Update stocks (maintaining consistency with store_product)
            $product->stock_s   = $request->stock_s;
            $product->stock_m   = $request->stock_m;
            $product->stock_l   = $request->stock_l;
            $product->stock_xl  = $request->stock_xl;
            $product->stock_2xl = $request->stock_2xl;
            $product->quantity  = $request->quantity;

            // Update category (get category name like in store_product)
            if ($request->category) {
                $category = Category::findOrFail($request->category);
                $product->category = $category->category_name; // Store the name instead of ID
            }

            // Handle image uploads for all image types (same logic as store_product)
            foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
                if ($request->hasFile($field)) {
                    // Delete old image if it exists
                    if ($product->$field && file_exists(public_path('product/' . $product->$field))) {
                        unlink(public_path('product/' . $product->$field));
                    }

                    // Upload new image
                    $filename = time().'_'.$field.'.'.$request->$field->getClientOriginalExtension();
                    $request->$field->move(public_path('product'), $filename);
                    $product->$field = $filename;
                }
            }

            $product->save();

            return redirect()->back()->with('success', 'Product updated successfully!');
        }

        // Optional: Add a method to handle individual image deletion
        public function delete_product_image($id, $imageType, Request $request)
        {
            $product = Product::findOrFail($id);
            
            // Validate image type
            $allowedTypes = ['image', 'hover_image', 'closeup_image', 'model_image'];
            if (!in_array($imageType, $allowedTypes)) {
                return redirect()->back()->with('error', 'Invalid image type.');
            }

            // Delete the image file and update database
            if ($product->$imageType && file_exists(public_path('product/' . $product->$imageType))) {
                unlink(public_path('product/' . $product->$imageType));
                $product->$imageType = null;
                $product->save();
                
                return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $imageType)) . ' deleted successfully!');
            }

            return redirect()->back()->with('error', 'Image not found.');
        }

        
 
}
