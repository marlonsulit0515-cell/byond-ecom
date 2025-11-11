@extends('layouts.dashboard')

@section('maincontent')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.add-product') }}" 
               class="btn-primary-color btn-md">
                Add New Product
            </a>

            <a href="{{ route('admin.trashed-products') }}" 
               class="btn-secondary-color btn-md">
                View Trash
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

  <div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Discount</th>
                <th class="text-center">S</th>
                <th class="text-center">M</th>
                <th class="text-center">L</th>
                <th class="text-center">XL</th>
                <th class="text-center">2XL</th>
                <th class="text-center">Total</th>
                <th class="text-center">Edit</th>
                <th class="text-center">Delete</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($product as $products)
            <tr>
                <td class="text-center">
                    @php
                        $images = [
                            ['src' => '/product/' . $products->image, 'label' => 'Main Image']
                        ];
                        if($products->hover_image) {
                            $images[] = ['src' => '/product/' . $products->hover_image, 'label' => 'Hover View'];
                        }
                        if($products->closeup_image) {
                            $images[] = ['src' => '/product/' . $products->closeup_image, 'label' => 'Close-up View'];
                        }
                        if($products->model_image) {
                            $images[] = ['src' => '/product/' . $products->model_image, 'label' => 'Model View'];
                        }
                    @endphp
                    <img src="/product/{{ $products->image }}" 
                         alt="{{ $products->name }}" 
                         class="table-image"
                         data-product-name="{{ $products->name }}"
                         data-images='@json($images)'>
                </td>
                <td class="text-primary">{{ $products->name }}</td>
                <td class="text-secondary">{{ Str::limit($products->description, 40) }}</td>
                <td class="text-secondary">{{ $products->category }}</td>
                <td class="text-price">₱{{ number_format($products->price, 2) }}</td>
                <td class="text-discount">
                    @if($products->discount_price)
                        ₱{{ number_format($products->discount_price, 2) }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-center text-secondary">{{ $products->stock_s ?? 0 }}</td>
                <td class="text-center text-secondary">{{ $products->stock_m ?? 0 }}</td>
                <td class="text-center text-secondary">{{ $products->stock_l ?? 0 }}</td>
                <td class="text-center text-secondary">{{ $products->stock_xl ?? 0 }}</td>
                <td class="text-center text-secondary">{{ $products->stock_2xl ?? 0 }}</td>
                <td class="text-center text-stock">
                    {{ $products->quantity ?? 0 }}
                </td>
                <td class="text-center">
                    <a href="{{ url('/update_product', $products->id) }}" 
                       class="action-btn action-btn-edit">
                        Edit
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ url('/delete_product', $products->id) }}" 
                       onclick="return confirm('Are you sure you want to move this product to trash?')" 
                       class="action-btn action-btn-delete">
                        Delete
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="empty-state">
                    <div class="empty-state-content">
                        <div class="empty-state-icon">📦</div>
                        <h3 class="empty-state-title">No products found</h3>
                        <p class="empty-state-text">
                            <a href="{{ route('admin.add-product') }}" class="empty-state-link">Add your first product</a>
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

    @if($product->hasPages())
        <div class="mt-8 mb-12 flex justify-center">
            {{ $product->appends(request()->input())->links('vendor.pagination.pagination-custom') }}
        </div>
    @endif

</div>

<div id="imageModal" 
     class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center transition-opacity duration-300"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="imageLabel">

    <div class="relative max-w-5xl w-[90%] max-h-[95vh]">

        {{-- Close Button --}}
        <button id="closeBtn" 
                class="absolute -top-10 right-0 md:-right-10 text-white text-4xl leading-none font-light hover:text-gray-400 transition-colors p-2"
                aria-label="Close Gallery">
            &times;
        </button>

        {{-- Navigation Button: Previous --}}
        <button id="prevBtn" 
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-900 bg-opacity-30 hover:bg-opacity-50 text-white w-12 h-12 rounded-full hidden items-center justify-center transition-all duration-200 ml-2 md:-ml-14"
                aria-label="Previous Image">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        {{-- Image Container --}}
        <div class="bg-gray-800 rounded-lg shadow-2xl overflow-hidden p-4 md:p-6 flex flex-col items-center">
            <img id="modalImage" 
                 class="max-w-full max-h-[70vh] object-contain rounded-md" 
                 src="" 
                 alt="Product Image Gallery">
            
            {{-- Caption/Label --}}
            <p id="imageLabel" class="text-white text-center mt-4 text-sm md:text-base font-medium"></p>
        </div>

        {{-- Navigation Button: Next --}}
        <button id="nextBtn" 
                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-900 bg-opacity-30 hover:bg-opacity-50 text-white w-12 h-12 rounded-full hidden items-center justify-center transition-all duration-200 mr-2 md:-mr-14"
                aria-label="Next Image">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</div>


<script src="{{ asset('script/admin-product-table.js') }}"></script>

@endsection