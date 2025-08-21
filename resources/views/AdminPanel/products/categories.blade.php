@extends('layouts.dashboard')
<!-- Login Section -->
@section('maincontent')
<div class="category-list">
    <h1>Byond Clothing </h1>
    <h2>Product Categories</h2>
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        
    @endif
    @if ($errors->any())
    <div class="alert alert-danger bg-red-500 text-white p-3 rounded mb-3">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form action="{{ url('/view_category') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <label for="category">Add New Category:</label><br>
        <input type="text" name="category" id="category" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" name="submit" value="Add Category">Add Category</button>
    </form>
    
    <table class="center">
        <tr>
            <td>Category</td>
            <td>Action</td>
        </tr>
        @foreach($data as $category)
    <tr>
        <td>{{ $category->category_name }}</td>
        <td>
         <a href="{{ route('admin.delete-category', $category->id) }}" 
            class="btn btn-danger">Delete</a>
    </td>
</tr>
@endforeach
    </table>
</div>
@endsection