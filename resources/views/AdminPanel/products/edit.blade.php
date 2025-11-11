@extends('layouts.dashboard')

@section('maincontent')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Product</h1>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="toast">
            {{ session()->get('success') }}
        </div>
    @endif

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="toast bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('update_confirmation', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="editProductForm">
        @csrf

        {{-- Product Info --}}
        <div>
            <label class="block font-medium mb-1">Product Name: <span class="text-red-500">*</span></label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $product->name) }}" 
                   required 
                   maxlength="255"
                   class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                   placeholder="Enter product name">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-1">Product Description:</label>
            <textarea name="description" 
                      rows="4"
                      maxlength="1000"
                      class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                      placeholder="Enter product description (optional)">{{ old('description', $product->description) }}</textarea>
            <p class="text-gray-500 text-sm mt-1">Maximum 1000 characters</p>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Images --}}
        <div class="border-t pt-4">
            <h2 class="text-lg font-semibold mb-3">Product Images</h2>
            <p class="text-gray-600 text-sm mb-4">Accepted formats: JPEG, PNG, JPG, GIF, WebP. Leave empty to keep current image.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Main Image --}}
                <div>
                    <label class="block font-medium mb-2">Main Image:</label>
                    @if($product->image)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                            <img src="/product/{{ $product->image }}" 
                                 alt="Current Image" 
                                 class="max-w-xs max-h-48 rounded border object-cover">
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No image uploaded</p>
                    @endif
                    <input type="file" 
                           name="image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           class="border rounded p-2 w-full @error('image') border-red-500 @enderror"
                           onchange="previewImage(this, 'mainPreview')">
                    <img id="mainPreview" class="mt-2 hidden max-w-xs max-h-48 rounded border">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hover Image --}}
                <div>
                    <label class="block font-medium mb-2">Hover Image:</label>
                    @if($product->hover_image)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                            <img src="/product/{{ $product->hover_image }}" 
                                 alt="Current Hover Image" 
                                 class="max-w-xs max-h-48 rounded border object-cover">
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No hover image uploaded</p>
                    @endif
                    <input type="file" 
                           name="hover_image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           class="border rounded p-2 w-full @error('hover_image') border-red-500 @enderror"
                           onchange="previewImage(this, 'hoverPreview')">
                    <img id="hoverPreview" class="mt-2 hidden max-w-xs max-h-48 rounded border">
                    @error('hover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Closeup Image --}}
                <div>
                    <label class="block font-medium mb-2">Close-up Image:</label>
                    @if($product->closeup_image)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                            <img src="/product/{{ $product->closeup_image }}" 
                                 alt="Current Closeup Image" 
                                 class="max-w-xs max-h-48 rounded border object-cover">
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No closeup image uploaded</p>
                    @endif
                    <input type="file" 
                           name="closeup_image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           class="border rounded p-2 w-full @error('closeup_image') border-red-500 @enderror"
                           onchange="previewImage(this, 'closeupPreview')">
                    <img id="closeupPreview" class="mt-2 hidden max-w-xs max-h-48 rounded border">
                    @error('closeup_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model Image --}}
                <div>
                    <label class="block font-medium mb-2">Model Image:</label>
                    @if($product->model_image)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                            <img src="/product/{{ $product->model_image }}" 
                                 alt="Current Model Image" 
                                 class="max-w-xs max-h-48 rounded border object-cover">
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No model image uploaded</p>
                    @endif
                    <input type="file" 
                           name="model_image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           class="border rounded p-2 w-full @error('model_image') border-red-500 @enderror"
                           onchange="previewImage(this, 'modelPreview')">
                    <img id="modelPreview" class="mt-2 hidden max-w-xs max-h-48 rounded border">
                    @error('model_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Category --}}
        <div class="border-t pt-4">
            <label class="block font-medium mb-1">Product Category: <span class="text-red-500">*</span></label>
            <select name="category" 
                    required 
                    class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                <option value="">-- Select Category --</option>
                @foreach ($category as $categories)
                    <option value="{{ $categories->id }}" 
                        {{ (old('category') == $categories->id || $product->category == $categories->category_name) ? 'selected' : '' }}>
                        {{ $categories->category_name }}
                    </option>
                @endforeach
            </select>
            @error('category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pricing --}}
        <div class="border-t pt-4">
            <h2 class="text-lg font-semibold mb-3">Pricing</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-1">Price (₱): <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="price" 
                           id="price"
                           min="1" 
                           max="10000"
                           step="0.01"
                           value="{{ old('price', $product->price) }}" 
                           required 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                           placeholder="0.00"
                           oninput="validateDiscountPrice()">
                    <p class="text-gray-500 text-sm mt-1">Maximum: ₱10,000</p>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-1">Discount Price (₱):</label>
                    <input type="number" 
                           name="dis_price" 
                           id="dis_price"
                           min="0" 
                           max="10000"
                           step="0.01"
                           value="{{ old('dis_price', $product->discount_price) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('dis_price') border-red-500 @enderror"
                           placeholder="0.00"
                           oninput="validateDiscountPrice()">
                    <p class="text-gray-500 text-sm mt-1">Must be lower than regular price</p>
                    <p id="discountError" class="text-red-500 text-sm mt-1 hidden"></p>
                    @error('dis_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Stock per size --}}
        <div class="border-t pt-4">
            <h2 class="text-lg font-semibold mb-3">Stock per Size</h2>
            <p class="text-gray-600 text-sm mb-4">Enter stock quantity for each size (leave empty or 0 if not applicable)</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block font-medium mb-1">Small (S):</label>
                    <input type="number" 
                           name="stock_s" 
                           min="0" 
                           max="9999"
                           value="{{ old('stock_s', $product->stock_s) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('stock_s') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock_s')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">Medium (M):</label>
                    <input type="number" 
                           name="stock_m" 
                           min="0" 
                           max="9999"
                           value="{{ old('stock_m', $product->stock_m) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('stock_m') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock_m')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">Large (L):</label>
                    <input type="number" 
                           name="stock_l" 
                           min="0" 
                           max="9999"
                           value="{{ old('stock_l', $product->stock_l) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('stock_l') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock_l')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">X-Large (XL):</label>
                    <input type="number" 
                           name="stock_xl" 
                           min="0" 
                           max="9999"
                           value="{{ old('stock_xl', $product->stock_xl) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('stock_xl') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock_xl')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">2XL:</label>
                    <input type="number" 
                           name="stock_2xl" 
                           min="0" 
                           max="9999"
                           value="{{ old('stock_2xl', $product->stock_2xl) }}" 
                           class="border rounded p-2 w-full focus:ring-2 focus:ring-blue-500 @error('stock_2xl') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock_2xl')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <strong>Note:</strong> Total quantity will be automatically calculated from individual size stocks.
            </p>
        </div>

        <div class="border-t pt-4 flex gap-3">
            <button type="submit" class="btn-primary-color btn-lg">
                Save Changes
            </button>
            <button type="button" onclick="window.history.back()" class="btn-secondary-color btn-lg">
                Cancel
            </button>
        </div>
    </form>
</div>

@endsection