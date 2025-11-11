@extends('layouts.dashboard')

@section('maincontent')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Deleted Categories</h1>
        <a href="{{ route('admin.categories') }}" 
           class="px-4 py-2 rounded-lg bg-[#1f0c35] hover:bg-black text-white font-semibold shadow-md transition">
            Back to Categories
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
                    <th class="p-3 text-left">Category Name</th>
                    <th class="p-3 text-left">Deleted At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 font-medium text-gray-900">{{ $category->category_name }}</td>
                    <td class="p-3 text-gray-600">{{ $category->deleted_at->format('M d, Y h:i A') }}</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.restore-category', $category->id) }}" 
                               onclick="return confirm('Are you sure you want to restore this category?')"
                               class="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow">
                                Restore
                            </a>
                            <a href="{{ route('admin.force-delete-category', $category->id) }}" 
                               onclick="return confirm('This will permanently delete this category. This action cannot be undone. Are you sure?')"
                               class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow">
                                Delete Forever
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-6 text-center text-gray-500">
                        No deleted categories found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection