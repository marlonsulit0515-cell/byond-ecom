@extends('layouts.dashboard')
@section('maincontent')

<div class="p-8">
    <h1 class="text-2xl font-bold mb-6">Shipping Rates Management</h1>

    <!-- FIXED QUANTITY RATES SECTION -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Fixed Rates by Quantity</h2>
                <p class="text-sm text-gray-600">Set exact shipping fees for specific item quantities</p>
            </div>
            <button onclick="addNewFixedRate()" class="btn-primary-color btn-lg">
                + Add Fixed Rate
            </button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Quantity Range</th>
                        <th>Fixed Price (₱)</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="fixedRatesTable">
                    @forelse($fixedRates as $rate)
                        <tr id="fixed-row-{{ $rate->id }}">
                            <td>
                                <div class="flex items-center gap-2">
                                    <input type="number" value="{{ $rate->quantity_from }}" 
                                        class="border p-1 w-20 rounded" id="fixed-qty-from-{{ $rate->id }}" min="1">
                                    <span class="text-secondary">to</span>
                                    <input type="number" value="{{ $rate->quantity_to }}" 
                                        class="border p-1 w-20 rounded" id="fixed-qty-to-{{ $rate->id }}" min="1">
                                    <span class="text-muted">items</span>
                                </div>
                            </td>
                            <td>
                                <input type="number" step="0.01" value="{{ $rate->fixed_price }}" 
                                    class="border p-1 w-full rounded" id="fixed-price-{{ $rate->id }}">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" {{ $rate->is_active ? 'checked' : '' }} 
                                    id="fixed-active-{{ $rate->id }}">
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="action-btn action-btn-edit" 
                                            onclick="updateFixedRate({{ $rate->id }})">Save</button>
                                    <button class="action-btn action-btn-delete" 
                                            onclick="deleteFixedRate({{ $rate->id }})">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="no-fixed-data">
                            <td colspan="4" class="text-center">
                                <span class="text-muted">No fixed rates configured. Click "Add Fixed Rate" to start.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-4 mt-4">
            <strong class="text-sm font-semibold text-gray-700">How Fixed Rates Work:</strong>
            <ul class="text-sm text-gray-600 mt-2 space-y-1">
                <li>• Set exact shipping fees for specific quantity ranges</li>
                <li>• Example: <strong>1-1 items = ₱100</strong>, <strong>2-5 items = ₱180</strong>, <strong>6-10 items = ₱330</strong></li>
                <li>• Customer's total quantity will match these ranges automatically</li>
                <li>• If no range matches, the province base rate below will be used</li>
            </ul>
        </div>
    </div>

    <!-- PROVINCE BASE RATES SECTION -->
    <div>
        <h2 class="text-xl font-semibold mb-4">Province Base Rates (Fallback)</h2>
        <p class="text-sm text-gray-600 mb-4">Used when no fixed quantity rate matches</p>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Price (₱)</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shippingRates as $rate)
                        <tr id="row-{{ $rate->id }}">
                            <td>
                                <input type="text" value="{{ $rate->province }}" 
                                    class="border p-1 w-full rounded" id="province-{{ $rate->id }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" value="{{ $rate->price }}" 
                                    class="border p-1 w-full rounded" id="price-{{ $rate->id }}">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" {{ $rate->is_active ? 'checked' : '' }} 
                                    id="active-{{ $rate->id }}">
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="action-btn action-btn-edit" 
                                            onclick="updateRate({{ $rate->id }})">Save</button>
                                    <button class="action-btn action-btn-delete" 
                                            onclick="deleteRate({{ $rate->id }})">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('script/shipping-setting.js') }}"></script>
@endsection