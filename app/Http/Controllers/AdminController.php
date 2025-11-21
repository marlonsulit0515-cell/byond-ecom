<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        return view('AdminPanel.products.index');
    }

    public function view_category()
    {   
        // Only get non-deleted categories
        $data = Category::all();
        return view('AdminPanel.products.categories', compact('data'));
    }

    public function registered_users()
    {
        $users = User::paginate(30)->appends(request()->query());
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
        // This will now soft delete instead of permanently deleting
        $data->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

    // NEW: Restore soft-deleted category
    public function restore_category($id)
    {
        $data = Category::withTrashed()->findOrFail($id);
        $data->restore();

        return redirect()->back()->with('success', 'Category restored successfully!');
    }

    // NEW: Permanently delete category
    public function force_delete_category($id)
    {
        $data = Category::withTrashed()->findOrFail($id);
        $data->forceDelete();

        return redirect()->back()->with('success', 'Category permanently deleted!');
    }

    // NEW: View trashed categories
    public function trashed_categories()
    {
        $data = Category::onlyTrashed()->get();
        return view('AdminPanel.products.trashed-categories', compact('data'));
    }

    public function add_product(Request $request)
    {
        // Only show non-deleted categories
        $category = Category::all();
        return view('AdminPanel.products.create', compact('category'));
    }

    public function store_product(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name',
            'description'   => 'nullable|string|max:1000',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'hover_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'model_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'category'      => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:1|max:10000',
            'stock_s'       => 'nullable|integer|min:0|max:9999',
            'stock_m'       => 'nullable|integer|min:0|max:9999',
            'stock_l'       => 'nullable|integer|min:0|max:9999',
            'stock_xl'      => 'nullable|integer|min:0|max:9999',
            'stock_2xl'     => 'nullable|integer|min:0|max:9999',
            'dis_price'     => [
                'nullable',
                'numeric',
                'min:0',
                'max:10000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '' && $value > 0) {
                        // Ensure price is provided when discount price is set
                        if (empty($request->price) || $request->price <= 0) {
                            $fail('Regular price must be set before adding a discount price.');
                        }
                        // Ensure discount price is lower than regular price
                        elseif ($value >= $request->price) {
                            $fail('Discount price must be lower than the regular price.');
                        }
                    }
                },
            ],
        ], [
            'name.required'        => 'Product name is required.',
            'name.unique'          => 'A product with this name already exists.',
            'name.max'             => 'Product name cannot exceed 255 characters.',
            'description.max'      => 'Description cannot exceed 1000 characters.',
            'price.required'       => 'Price is required.',
            'price.numeric'        => 'Price must be a valid number.',
            'price.min'            => 'Price must be at least ₱1.',
            'price.max'            => 'Price cannot exceed ₱10,000.',
            'dis_price.numeric'    => 'Discount price must be a valid number.',
            'dis_price.max'        => 'Discount price cannot exceed ₱10,000.',
            'category.required'    => 'Please select a category.',
            'category.exists'      => 'Selected category is invalid.',
            'image.image'          => 'Main image must be a valid image file.',
            'image.mimes'          => 'Main image must be: jpeg, png, jpg, gif, or webp.',
            'hover_image.image'    => 'Hover image must be a valid image file.',
            'hover_image.mimes'    => 'Hover image must be: jpeg, png, jpg, gif, or webp.',
            'closeup_image.image'  => 'Close-up image must be a valid image file.',
            'closeup_image.mimes'  => 'Close-up image must be: jpeg, png, jpg, gif, or webp.',
            'model_image.image'    => 'Model image must be a valid image file.',
            'model_image.mimes'    => 'Model image must be: jpeg, png, jpg, gif, or webp.',
            'stock_s.integer'      => 'Stock S must be a whole number.',
            'stock_s.min'          => 'Stock S cannot be negative.',
            'stock_s.max'          => 'Stock S cannot exceed 9999.',
            'stock_m.integer'      => 'Stock M must be a whole number.',
            'stock_m.min'          => 'Stock M cannot be negative.',
            'stock_m.max'          => 'Stock M cannot exceed 9999.',
            'stock_l.integer'      => 'Stock L must be a whole number.',
            'stock_l.min'          => 'Stock L cannot be negative.',
            'stock_l.max'          => 'Stock L cannot exceed 9999.',
            'stock_xl.integer'     => 'Stock XL must be a whole number.',
            'stock_xl.min'         => 'Stock XL cannot be negative.',
            'stock_xl.max'         => 'Stock XL cannot exceed 9999.',
            'stock_2xl.integer'    => 'Stock 2XL must be a whole number.',
            'stock_2xl.min'        => 'Stock 2XL cannot be negative.',
            'stock_2xl.max'        => 'Stock 2XL cannot exceed 9999.',
        ]);

        try {
            // Start database transaction
            DB::beginTransaction();

            $product = new Product();
            
            // Sanitize and set basic fields
            $product->name           = trim($request->name);
            $product->description    = $request->description ? trim($request->description) : null;
            $product->price          = round($request->price, 2);
            $product->discount_price = ($request->dis_price && $request->dis_price > 0) 
                                        ? round($request->dis_price, 2) 
                                        : null;

            // Set stock levels with default 0
            $product->stock_s   = $request->stock_s ?? 0;
            $product->stock_m   = $request->stock_m ?? 0;
            $product->stock_l   = $request->stock_l ?? 0;
            $product->stock_xl  = $request->stock_xl ?? 0;
            $product->stock_2xl = $request->stock_2xl ?? 0;

            // Calculate total quantity
            $product->quantity = $product->stock_s + 
                            $product->stock_m + 
                            $product->stock_l + 
                            $product->stock_xl + 
                            $product->stock_2xl;

            // Get category name
            $category = Category::findOrFail($request->category);
            $product->category = $category->category_name;

            // Handle image uploads with comprehensive error checking
            $uploadedImages = [];
            $imageFields = ['image', 'hover_image', 'closeup_image', 'model_image'];
            
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    
                    // Validate file
                    if (!$file->isValid()) {
                        throw new \Exception("Failed to upload {$field}. The file may be corrupted or too large.");
                    }
                    
                    // Generate unique filename
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '_' . $field . '.' . $extension;
                    
                    // Ensure product directory exists
                    $uploadPath = public_path('product');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    // Move file to public directory
                    try {
                        $file->move($uploadPath, $filename);
                        $product->$field = $filename;
                        $uploadedImages[] = $filename;
                    } catch (\Exception $e) {
                        throw new \Exception("Failed to save {$field} to server. " . $e->getMessage());
                    }
                }
            }

            // Save product to database
            $product->save();

            // Commit transaction
            DB::commit();

            return redirect()->back()->with('success', 'Product added successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are handled automatically by Laravel
            throw $e;
            
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            // Clean up uploaded images if database save fails
            if (isset($uploadedImages) && !empty($uploadedImages)) {
                foreach ($uploadedImages as $img) {
                    $filePath = public_path('product/' . $img);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }
            
            // Log error for debugging
            Log::error('Product creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to add product: ' . $e->getMessage()]);
        }
    }

    public function show_product()
    {
        // Only get non-deleted products
        $product = Product::orderBy('created_at', 'desc')->paginate(30);
        return view('AdminPanel.products.store', compact('product'));
    }

    // NEW: View trashed products
    public function trashed_products()
    {
        $product = Product::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(30);
        return view('AdminPanel.products.trashed-products', compact('product'));
    }

    public function update_product($id)
    {
        $product = Product::findOrFail($id);
        // Only show non-deleted categories
        $category = Category::all();
        return view('AdminPanel.products.edit', compact('product', 'category')); 
    }

    public function update_confirmation($id, Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name,' . $id,
            'description'   => 'nullable|string|max:1000',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'hover_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'closeup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'model_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'category'      => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:1|max:10000',
            'stock_s'       => 'nullable|integer|min:0|max:9999',
            'stock_m'       => 'nullable|integer|min:0|max:9999',
            'stock_l'       => 'nullable|integer|min:0|max:9999',
            'stock_xl'      => 'nullable|integer|min:0|max:9999',
            'stock_2xl'     => 'nullable|integer|min:0|max:9999',
            'dis_price'     => [
                'nullable',
                'numeric',
                'min:0',
                'max:10000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '' && $value > 0) {
                        // Ensure price is provided when discount price is set
                        if (empty($request->price) || $request->price <= 0) {
                            $fail('Regular price must be set before adding a discount price.');
                        }
                        // Ensure discount price is lower than regular price
                        elseif ($value >= $request->price) {
                            $fail('Discount price must be lower than the regular price.');
                        }
                    }
                },
            ],
        ], [
            'name.required'        => 'Product name is required.',
            'name.unique'          => 'A product with this name already exists.',
            'name.max'             => 'Product name cannot exceed 255 characters.',
            'description.max'      => 'Description cannot exceed 1000 characters.',
            'price.required'       => 'Price is required.',
            'price.numeric'        => 'Price must be a valid number.',
            'price.min'            => 'Price must be at least ₱1.',
            'price.max'            => 'Price cannot exceed ₱10,000.',
            'dis_price.numeric'    => 'Discount price must be a valid number.',
            'dis_price.max'        => 'Discount price cannot exceed ₱10,000.',
            'category.required'    => 'Please select a category.',
            'category.exists'      => 'Selected category is invalid.',
            'image.image'          => 'Main image must be a valid image file.',
            'image.mimes'          => 'Main image must be: jpeg, png, jpg, gif, or webp.',
            'hover_image.image'    => 'Hover image must be a valid image file.',
            'hover_image.mimes'    => 'Hover image must be: jpeg, png, jpg, gif, or webp.',
            'closeup_image.image'  => 'Close-up image must be a valid image file.',
            'closeup_image.mimes'  => 'Close-up image must be: jpeg, png, jpg, gif, or webp.',
            'model_image.image'    => 'Model image must be a valid image file.',
            'model_image.mimes'    => 'Model image must be: jpeg, png, jpg, gif, or webp.',
            'stock_s.integer'      => 'Stock S must be a whole number.',
            'stock_s.min'          => 'Stock S cannot be negative.',
            'stock_s.max'          => 'Stock S cannot exceed 9999.',
            'stock_m.integer'      => 'Stock M must be a whole number.',
            'stock_m.min'          => 'Stock M cannot be negative.',
            'stock_m.max'          => 'Stock M cannot exceed 9999.',
            'stock_l.integer'      => 'Stock L must be a whole number.',
            'stock_l.min'          => 'Stock L cannot be negative.',
            'stock_l.max'          => 'Stock L cannot exceed 9999.',
            'stock_xl.integer'     => 'Stock XL must be a whole number.',
            'stock_xl.min'         => 'Stock XL cannot be negative.',
            'stock_xl.max'         => 'Stock XL cannot exceed 9999.',
            'stock_2xl.integer'    => 'Stock 2XL must be a whole number.',
            'stock_2xl.min'        => 'Stock 2XL cannot be negative.',
            'stock_2xl.max'        => 'Stock 2XL cannot exceed 9999.',
        ]);

        try {
            // Start database transaction
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            // Sanitize and update basic fields
            $product->name           = trim($request->name);
            $product->description    = $request->description ? trim($request->description) : null;
            $product->price          = round($request->price, 2);
            $product->discount_price = ($request->dis_price && $request->dis_price > 0) 
                                        ? round($request->dis_price, 2) 
                                        : null;

            // Update stock levels with default 0
            $product->stock_s   = $request->stock_s ?? 0;
            $product->stock_m   = $request->stock_m ?? 0;
            $product->stock_l   = $request->stock_l ?? 0;
            $product->stock_xl  = $request->stock_xl ?? 0;
            $product->stock_2xl = $request->stock_2xl ?? 0;

            // Calculate total quantity from individual sizes
            $product->quantity = $product->stock_s + 
                            $product->stock_m + 
                            $product->stock_l + 
                            $product->stock_xl + 
                            $product->stock_2xl;

            // Update category
            if ($request->category) {
                $category = Category::findOrFail($request->category);
                $product->category = $category->category_name;
            }

            // Handle image uploads with comprehensive error checking
            $uploadedImages = [];
            $oldImages = [];
            $imageFields = ['image', 'hover_image', 'closeup_image', 'model_image'];
            
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    
                    // Validate file
                    if (!$file->isValid()) {
                        throw new \Exception("Failed to upload {$field}. The file may be corrupted or too large.");
                    }
                    
                    // Store old image path for deletion
                    if ($product->$field && file_exists(public_path('product/' . $product->$field))) {
                        $oldImages[$field] = $product->$field;
                    }
                    
                    // Generate unique filename
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '_' . $field . '.' . $extension;
                    
                    // Ensure product directory exists
                    $uploadPath = public_path('product');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    // Move file to public directory
                    try {
                        $file->move($uploadPath, $filename);
                        $product->$field = $filename;
                        $uploadedImages[] = $filename;
                    } catch (\Exception $e) {
                        throw new \Exception("Failed to save {$field} to server. " . $e->getMessage());
                    }
                }
            }

            // Save product to database
            $product->save();

            // Only delete old images after successful save
            foreach ($oldImages as $oldImage) {
                $oldPath = public_path('product/' . $oldImage);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Product updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are handled automatically by Laravel
            throw $e;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Product not found
            DB::rollBack();
            
            return redirect()->route('admin.show-product')
                ->withErrors(['error' => 'Product not found.']);
                
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            // Clean up newly uploaded images if database save fails
            if (isset($uploadedImages) && !empty($uploadedImages)) {
                foreach ($uploadedImages as $img) {
                    $filePath = public_path('product/' . $img);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }
            
            // Log error for debugging
            Log::error('Product update failed: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    public function delete_product($id)
    {
        $product = Product::findOrFail($id);
        // This will now soft delete instead of permanently deleting
        $product->delete();

        return redirect()->back()->with('success', 'Product moved to trash!');
    }

    // NEW: Restore soft-deleted product
    public function restore_product($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->back()->with('success', 'Product restored successfully!');
    }

    // NEW: Permanently delete product
    public function force_delete_product($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        // Check if product has any associated orders
        $hasOrders = DB::table('order_items')
            ->where('product_id', $id)
            ->exists();

        if ($hasOrders) {
            return redirect()->back()->with('error', 'Cannot permanently delete this product because it has associated orders. The product will remain in trash to preserve order history.');
        }

        // Delete all related images if they exist
        foreach (['image', 'hover_image', 'closeup_image', 'model_image'] as $field) {
            if ($product->$field && file_exists(public_path('product/' . $product->$field))) {
                unlink(public_path('product/' . $product->$field));
            }
        }
        
        $product->forceDelete();

        return redirect()->back()->with('success', 'Product permanently deleted!');
    }

    public function inbox()
    {
        return view('AdminPanel.support.inbox');
    }
}