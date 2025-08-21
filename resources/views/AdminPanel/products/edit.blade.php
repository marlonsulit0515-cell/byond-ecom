@extends('layouts.dashboard')

@section('maincontent')
<link href="{{ asset('css/admin-create-product.css') }}" rel="stylesheet" />
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Product</h1>
    
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ url('update_confirmation', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Product Name -->
        <label>Product Name:</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required><br>

        <!-- Product Description -->
        <label>Product Description:</label>
        <textarea name="description">{{ old('description', $product->description) }}</textarea><br>

        <!-- Main Product Image -->
        <label>Current Product Image</label>
        @if($product->image)
            <img style="margin:auto;" height="150px" width="200px" src="/product/{{ $product->image }}" alt="Current Image">
        @else
            <p>No image uploaded</p>
        @endif
        
        <label>Change Product Image</label>
        <input type="file" name="image"><br>

        <!-- Hover Image -->
        <label>Current Hover Image</label>
        @if($product->hover_image)
            <img style="margin:auto;" height="150px" width="200px" src="/product/{{ $product->hover_image }}" alt="Current Hover Image">
        @else
            <p>No hover image uploaded</p>
        @endif
        
        <label>Change Hover Image</label>
        <input type="file" name="hover_image"><br>

        <!-- Closeup Image -->
        <label>Current Closeup Image</label>
        @if($product->closeup_image)
            <img style="margin:auto;" height="150px" width="200px" src="/product/{{ $product->closeup_image }}" alt="Current Closeup Image">
        @else
            <p>No closeup image uploaded</p>
        @endif
        
        <label>Change Closeup Image</label>
        <input type="file" name="closeup_image"><br>

        <!-- Model Image -->
        <label>Current Model Image</label>
        @if($product->model_image)
            <img style="margin:auto;" height="150px" width="200px" src="/product/{{ $product->model_image }}" alt="Current Model Image">
        @else
            <p>No model image uploaded</p>
        @endif
        
        <label>Change Model Image</label>
        <input type="file" name="model_image"><br>

        <!-- Product Category -->
        <label>Product Category:</label>
        <select name="category" required>
            @foreach ($category as $categories)
                <option value="{{ $categories->id }}" 
                    {{ $product->category == $categories->category_name ? 'selected' : '' }}>
                    {{ $categories->category_name }}
                </option>
            @endforeach
        </select><br>
        
        <!-- Price -->
        <label>Price:</label>
        <input type="number" name="price" min="1" step="0.01" value="{{ old('price', $product->price) }}" required><br>

        <!-- Stock Management -->
        <h3>Stock Management</h3>
        
        <label>Stock Size S:</label>
        <input type="number" name="stock_s" min="0" value="{{ old('stock_s', $product->stock_s) }}" placeholder="Small size stock"><br>

        <label>Stock Size M:</label>
        <input type="number" name="stock_m" min="0" value="{{ old('stock_m', $product->stock_m) }}" placeholder="Medium size stock"><br>

        <label>Stock Size L:</label>
        <input type="number" name="stock_l" min="0" value="{{ old('stock_l', $product->stock_l) }}" placeholder="Large size stock"><br>

        <label>Stock Size XL:</label>
        <input type="number" name="stock_xl" min="0" value="{{ old('stock_xl', $product->stock_xl) }}" placeholder="Extra Large size stock"><br>

        <label>Stock Size 2XL:</label>
        <input type="number" name="stock_2xl" min="0" value="{{ old('stock_2xl', $product->stock_2xl) }}" placeholder="2X Large size stock"><br>

        <label>General Quantity:</label>
        <input type="number" name="quantity" min="0" value="{{ old('quantity', $product->quantity) }}" placeholder="General product quantity"><br>

        <!-- Discount Price -->
        <label>Discount Price:</label>
        <input type="number" name="dis_price" min="0" step="0.01" value="{{ old('dis_price', $product->discount_price) }}" placeholder="Write Discount if applicable"><br>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</div>
@endsection