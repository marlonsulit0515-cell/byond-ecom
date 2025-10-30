@extends('layouts.dashboard')

@section('maincontent')
    <h1 class="text-2xl font-bold mb-4">Current Users</h1>

    <table class="min-w-full bg-white border border-gray-300 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b text-left">#</th>
                <th class="py-2 px-4 border-b text-left">Name</th>
                <th class="py-2 px-4 border-b text-left">Email</th>
                <th class="py-2 px-4 border-b text-left">User Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b">{{ $user->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                    <td class="py-2 px-4 border-b capitalize">{{ $user->usertype }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
