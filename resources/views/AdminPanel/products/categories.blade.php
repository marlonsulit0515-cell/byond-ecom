@extends('layouts.dashboard')

@section('maincontent')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Product Categories</h1>
        <a href="{{ route('admin.trashed-categories') }}" 
           class="btn-secondary-color btn-md">
            View Trash
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-500 text-white p-3 rounded mb-3">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ url('/view_category') }}" method="POST" class="mb-6 bg-white p-4 rounded-lg shadow">
        @csrf
        <div class="flex gap-3 items-end">
            <div class="flex-1">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Add New Category:</label>
                <input type="text" 
                       name="category" 
                       id="category" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1f0c35]">
            </div>
            <button type="submit" 
                    class="btn-primary-color btn-md">
                Add Category
            </button>
        </div>
    </form>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $category)
                <tr>
                    <td class="text-primary">{{ $category->category_name }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.delete-category', $category->id) }}" 
                        onclick="return confirm('Are you sure you want to delete this category?')"
                        class="action-btn action-btn-delete">
                            Delete
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="empty-state">
                        <div class="empty-state-content">
                            <div class="empty-state-icon">📁</div>
                            <h3 class="empty-state-title">No categories found</h3>
                            <p class="empty-state-text">Add your first category above.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection