@extends('layouts.dashboard')
<link href="{{ asset('css/admin-product-table.css') }}" rel="stylesheet" />
@section('maincontent')

<h1 class="text-xl font-bold text-center">Products</h1>
<a href="{{ route('admin.add-product') }}" class="add-product-link">Add New Product</a>

<table class="store-table mx-auto border border-gray-300 shadow-lg w-full text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2">Image</th>
            <th class="p-2">Product Name</th>
            <th class="p-2">Description</th>
            <th class="p-2">Category</th>
            <th class="p-2">Price</th>
            <th class="p-2">Discount Price</th>
            <th class="p-2">Stock (S)</th>
            <th class="p-2">Stock (M)</th>
            <th class="p-2">Stock (L)</th>
            <th class="p-2">Stock (XL)</th>
            <th class="p-2">Stock (2XL)</th>
            <th class="p-2">Total Stock</th>
            <th class="p-2">Edit</th>
            <th class="p-2">Delete</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($product as $products)
        <tr class="border-t">
            <td class="p-2 text-center">
                <!-- Main Thumbnail -->
                <img src="/product/{{ $products->image }}" 
                     alt="{{ $products->name }}" 
                     class="clickable-image w-16 h-16 object-cover rounded cursor-pointer"
                     data-product-name="{{ $products->name }}">

                <!-- Hidden spans with all available images -->
                <span class="product-images hidden">
                    @if($products->image)<span data-src="/product/{{ $products->image }}" data-label="Main Image"></span>@endif
                    @if($products->hover_image)<span data-src="/product/{{ $products->hover_image }}" data-label="Hover View"></span>@endif
                    @if($products->closeup_image)<span data-src="/product/{{ $products->closeup_image }}" data-label="Close-up View"></span>@endif
                    @if($products->model_image)<span data-src="/product/{{ $products->model_image }}" data-label="Model View"></span>@endif
                </span>
            </td>
            <td class="p-2">{{ $products->name }}</td>
            <td class="p-2">{{ Str::limit($products->description, 50) }}</td>
            <td class="p-2">{{ $products->category }}</td>
            <td class="p-2">₱{{ number_format($products->price, 2) }}</td>
            <td class="p-2">
                @if($products->discount_price)
                    ₱{{ number_format($products->discount_price, 2) }}
                @else
                    -
                @endif
            </td>
            <td class="p-2">{{ $products->stock_s ?? 0 }}</td>
            <td class="p-2">{{ $products->stock_m ?? 0 }}</td>
            <td class="p-2">{{ $products->stock_l ?? 0 }}</td>
            <td class="p-2">{{ $products->stock_xl ?? 0 }}</td>
            <td class="p-2">{{ $products->stock_2xl ?? 0 }}</td>
            <td class="p-2 font-semibold">
                {{ ($products->stock_s ?? 0) + ($products->stock_m ?? 0) + ($products->stock_l ?? 0) + ($products->stock_xl ?? 0) + ($products->stock_2xl ?? 0) }}
            </td>
            <td class="p-2">
                <a class="btn btn-success px-2 py-1 rounded bg-green-500 text-white"
                   href="{{ url('/update_product', $products->id) }}">
                   Edit
                </a>
            </td>
            <td class="p-2">
                <a class="btn btn-danger px-2 py-1 rounded bg-red-500 text-white" 
                   onclick="return confirm('Are you sure you want to delete this product?')" 
                   href="{{ url('/delete_product', $products->id) }}">
                   Delete
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Enhanced Modal Structure -->
<div id="imageModal" class="image-modal">
    <span id="closeBtn" class="image-modal-close">&times;</span>
    <div class="modal-container">
        <img id="modalImage" class="image-modal-content" />
        <div id="imageLabel" class="image-label"></div>
    </div>
    <div id="prevBtn" class="nav-btn prev-btn">&#8249;</div>
    <div id="nextBtn" class="nav-btn next-btn">&#8250;</div>
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