@extends('layouts.dashboard')

@section('maincontent')
<link href="{{ asset('css/admin-create-product.css') }}" rel="stylesheet" />
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Add New Product</h1>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
            {{ session()->get('success') }}
        </div>
    @endif

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.store-product') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Product Info --}}
        <div>
            <label class="block">Product Name:</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="border rounded p-2 w-full">
        </div>

        <div>
            <label class="block">Product Description:</label>
            <textarea name="description" class="border rounded p-2 w-full">{{ old('description') }}</textarea>
        </div>

        {{-- Images --}}
        <div>
            <label class="block">Main Image:</label>
            <input type="file" name="image" class="border rounded p-2 w-full">
        </div>

        <div>
            <label class="block">Hover Image:</label>
            <input type="file" name="hover_image" class="border rounded p-2 w-full">
        </div>

        <div>
            <label class="block">Close-up Image:</label>
            <input type="file" name="closeup_image" class="border rounded p-2 w-full">
        </div>

        <div>
            <label class="block">Model Image:</label>
            <input type="file" name="model_image" class="border rounded p-2 w-full">
        </div>

        {{-- Category --}}
        <div>
            <label class="block">Product Category:</label>
            <select name="category" required class="border rounded p-2 w-full">
                <option value="">-- Select Category --</option>
                @foreach ($category as $categories)
                    <option value="{{ $categories->id }}" {{ old('category') == $categories->id ? 'selected' : '' }}>
                        {{ $categories->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pricing --}}
        <div>
            <label class="block">Price:</label>
            <input type="number" name="price" min="1" value="{{ old('price') }}" required class="border rounded p-2 w-full">
        </div>

        <div>
            <label class="block">Discount Price:</label>
            <input type="number" name="dis_price" min="0" value="{{ old('dis_price') }}" class="border rounded p-2 w-full">
        </div>

        {{-- Stock per size --}}
        <h2 class="text-lg font-semibold mt-4">Stock per Size</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <label class="block">Small (S):</label>
                <input type="number" name="stock_s" min="0" value="{{ old('stock_s') }}" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block">Medium (M):</label>
                <input type="number" name="stock_m" min="0" value="{{ old('stock_m') }}" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block">Large (L):</label>
                <input type="number" name="stock_l" min="0" value="{{ old('stock_l') }}" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block">X-Large (XL):</label>
                <input type="number" name="stock_xl" min="0" value="{{ old('stock_xl') }}" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block">2XL:</label>
                <input type="number" name="stock_2xl" min="0" value="{{ old('stock_2xl') }}" class="border rounded p-2 w-full">
            </div>
        </div>

        {{-- Total Quantity (optional override) --}}
        <div>
            <label class="block">Total Quantity (optional):</label>
            <input type="number" name="quantity" min="0" value="{{ old('quantity') }}" class="border rounded p-2 w-full">
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>
</div>
@endsection
