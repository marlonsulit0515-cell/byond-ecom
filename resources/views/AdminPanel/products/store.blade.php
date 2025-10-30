@extends('layouts.dashboard')

@section('maincontent')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        {{-- Changed text-white to text-gray-900 assuming the main dashboard background is light, or adjust based on your layout --}}
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <a href="{{ route('admin.add-product') }}" 
           class="px-4 py-2 rounded-lg bg-[#1f0c35] hover:bg-black text-white font-semibold shadow-md transition">
            Add New Product
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-[#1f0c35] text-white uppercase text-xs tracking-wide">
                <tr>
                    <th class="p-3 text-left">Image</th>
                    <th class="p-3 text-left">Product Name</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-left">Discount</th>
                    <th class="p-3 text-center">S</th>
                    <th class="p-3 text-center">M</th>
                    <th class="p-3 text-center">L</th>
                    <th class="p-3 text-center">XL</th>
                    <th class="p-3 text-center">2XL</th>
                    <th class="p-3 text-center">Total</th>
                    <th class="p-3 text-center">Edit</th>
                    <th class="p-3 text-center">Delete</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($product as $products)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-center">
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
                             class="clickable-image w-14 h-14 object-cover rounded-md border border-gray-300 cursor-pointer"
                             data-product-name="{{ $products->name }}"
                             data-images='@json($images)'>
                    </td>
                    <td class="p-3 font-medium text-gray-900">{{ $products->name }}</td>
                    <td class="p-3 text-gray-600">{{ Str::limit($products->description, 40) }}</td>
                    <td class="p-3">{{ $products->category }}</td>
                    <td class="p-3 font-semibold text-gray-900">₱{{ number_format($products->price, 2) }}</td>
                    <td class="p-3 text-red-600">
                        @if($products->discount_price)
                            ₱{{ number_format($products->discount_price, 2) }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">{{ $products->stock_s ?? 0 }}</td>
                    <td class="p-3 text-center">{{ $products->stock_m ?? 0 }}</td>
                    <td class="p-3 text-center">{{ $products->stock_l ?? 0 }}</td>
                    <td class="p-3 text-center">{{ $products->stock_xl ?? 0 }}</td>
                    <td class="p-3 text-center">{{ $products->stock_2xl ?? 0 }}</td>
                    <td class="p-3 text-center font-bold text-[#1f0c35]">
                        {{ $products->quantity ?? 0 }}
                    </td>
                    <td class="p-3 text-center">
                        <a href="{{ url('/update_product', $products->id) }}" 
                           class="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow">
                            Edit
                        </a>
                    </td>
                    <td class="p-3 text-center">
                        <a href="{{ url('/delete_product', $products->id) }}" 
                           onclick="return confirm('Are you sure you want to delete this product?')" 
                           class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow">
                            Delete
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="p-6 text-center text-gray-500">
                        No products found. <a href="{{ route('admin.add-product') }}" class="text-[#1f0c35] hover:underline">Add your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $product->links() }}
    </div>
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