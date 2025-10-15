@extends('layouts.dashboard')

<script src="https://cdn.tailwindcss.com"></script>
@section('maincontent')
<!--Admin Table Displying Products-->
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white"> Products</h1>
        <a href="{{ route('admin.add-product') }}" 
           class="px-4 py-2 rounded-lg bg-[#1f0c35] hover:bg-black text-white font-semibold shadow-md transition">
            Add New Product
        </a>
    </div>

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
                @foreach ($product as $products)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-center">
                        <img src="/product/{{ $products->image }}" 
                             alt="{{ $products->name }}" 
                             class="clickable-image w-14 h-14 object-cover rounded-md border border-gray-300 cursor-pointer"
                             data-product-name="{{ $products->name }}">
                        <span class="product-images hidden">
                            @if($products->image)<span data-src="/product/{{ $products->image }}" data-label="Main Image"></span>@endif
                            @if($products->hover_image)<span data-src="/product/{{ $products->hover_image }}" data-label="Hover View"></span>@endif
                            @if($products->closeup_image)<span data-src="/product/{{ $products->closeup_image }}" data-label="Close-up View"></span>@endif
                            @if($products->model_image)<span data-src="/product/{{ $products->model_image }}" data-label="Model View"></span>@endif
                        </span>
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
                        {{ ($products->stock_s ?? 0) + ($products->stock_m ?? 0) + ($products->stock_l ?? 0) + ($products->stock_xl ?? 0) + ($products->stock_2xl ?? 0) }}
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Enhanced Modal Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const imageLabel = document.getElementById("imageLabel");
        const closeBtn = document.getElementById("closeBtn");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        let currentImages = [];
        let currentLabels = [];
        let currentIndex = 0;
        let productName = "";

        function updateModalContent() {
            if (currentImages.length > 0) {
                modalImg.src = currentImages[currentIndex];
                const label = currentLabels[currentIndex];
                imageLabel.textContent = `${productName} - ${label} (${currentIndex + 1}/${currentImages.length})`;
                
                // Show/hide navigation buttons based on image count
                prevBtn.style.display = currentImages.length > 1 ? 'flex' : 'none';
                nextBtn.style.display = currentImages.length > 1 ? 'flex' : 'none';
            }
        }

        document.querySelectorAll(".clickable-image").forEach(img => {
            img.addEventListener("click", function() {
                // Get product name
                productName = this.dataset.productName;
                
                // Get hidden spans inside this cell
                const spans = this.parentElement.querySelectorAll(".product-images span");
                currentImages = Array.from(spans).map(s => s.dataset.src);
                currentLabels = Array.from(spans).map(s => s.dataset.label);

                if (currentImages.length > 0) {
                    currentIndex = 0;
                    updateModalContent();
                    modal.style.display = 'block';
                }
            });
        });

        closeBtn.onclick = () => {
            modal.style.display = 'none';
        };

        prevBtn.onclick = () => {
            if (currentImages.length > 1) {
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                updateModalContent();
            }
        };

        nextBtn.onclick = () => {
            if (currentImages.length > 1) {
                currentIndex = (currentIndex + 1) % currentImages.length;
                updateModalContent();
            }
        };

        // Close modal when clicking outside the image
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (modal.style.display === 'block') {
                if (e.key === 'Escape') {
                    modal.style.display = 'none';
                } else if (e.key === 'ArrowLeft' && currentImages.length > 1) {
                    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                    updateModalContent();
                } else if (e.key === 'ArrowRight' && currentImages.length > 1) {
                    currentIndex = (currentIndex + 1) % currentImages.length;
                    updateModalContent();
                }
            }
        });
    });
</script>

@endsection