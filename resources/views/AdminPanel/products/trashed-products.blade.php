@extends('layouts.dashboard')

@section('maincontent')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Deleted Products</h1>
        <a href="{{ route('admin.show-product') }}" 
           class="px-4 py-2 rounded-lg bg-[#1f0c35] hover:bg-black text-white font-semibold shadow-md transition">
            Back to Products
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-600 text-white uppercase text-xs tracking-wide">
                <tr>
                    <th class="p-3 text-left">Image</th>
                    <th class="p-3 text-left">Product Name</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-center">Total Stock</th>
                    <th class="p-3 text-left">Deleted At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($product as $products)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-center">
                        <img src="/product/{{ $products->image }}" 
                             alt="{{ $products->name }}" 
                             class="w-14 h-14 object-cover rounded-md border border-gray-300">
                    </td>
                    <td class="p-3 font-medium text-gray-900">{{ $products->name }}</td>
                    <td class="p-3">{{ $products->category }}</td>
                    <td class="p-3 font-semibold text-gray-900">₱{{ number_format($products->price, 2) }}</td>
                    <td class="p-3 text-center font-bold text-gray-700">{{ $products->quantity ?? 0 }}</td>
                    <td class="p-3 text-gray-600">{{ $products->deleted_at->format('M d, Y h:i A') }}</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.restore-product', $products->id) }}" 
                               onclick="return confirm('Are you sure you want to restore this product?')"
                               class="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow">
                                Restore
                            </a>
                            <a href="{{ route('admin.force-delete-product', $products->id) }}" 
                               onclick="return confirm('This will permanently delete this product and all its images. This action cannot be undone. Are you sure?')"
                               class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow">
                                Delete Forever
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No deleted products found.
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
@endsection