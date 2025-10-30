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
            <button onclick="addNewFixedRate()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Add Fixed Rate
            </button>
        </div>

        <table class="min-w-full border border-gray-300 rounded-lg mb-4">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="py-3 px-4 border-b">Quantity Range</th>
                    <th class="py-3 px-4 border-b">Fixed Price (₱)</th>
                    <th class="py-3 px-4 border-b">Active</th>
                    <th class="py-3 px-4 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="fixedRatesTable">
                @forelse($fixedRates as $rate)
                    <tr id="fixed-row-{{ $rate->id }}" class="border-b">
                        <td class="py-2 px-4">
                            <div class="flex items-center gap-2">
                                <input type="number" value="{{ $rate->quantity_from }}" 
                                       class="border p-1 w-20 rounded" id="fixed-qty-from-{{ $rate->id }}" min="1">
                                <span class="text-gray-500">to</span>
                                <input type="number" value="{{ $rate->quantity_to }}" 
                                       class="border p-1 w-20 rounded" id="fixed-qty-to-{{ $rate->id }}" min="1">
                                <span class="text-sm text-gray-500">items</span>
                            </div>
                        </td>
                        <td class="py-2 px-4">
                            <input type="number" step="0.01" value="{{ $rate->fixed_price }}" 
                                   class="border p-1 w-full rounded" id="fixed-price-{{ $rate->id }}">
                        </td>
                        <td class="py-2 px-4">
                            <input type="checkbox" {{ $rate->is_active ? 'checked' : '' }} 
                                   id="fixed-active-{{ $rate->id }}">
                        </td>
                        <td class="py-2 px-4 text-center space-x-2">
                            <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" 
                                    onclick="updateFixedRate({{ $rate->id }})">Save</button>
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" 
                                    onclick="deleteFixedRate({{ $rate->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr id="no-fixed-data">
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                            No fixed rates configured. Click "Add Fixed Rate" to start.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <p class="text-sm font-semibold text-gray-700">💡 How Fixed Rates Work:</p>
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
        
        <table class="min-w-full border border-gray-300 rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="py-3 px-4 border-b">Province</th>
                    <th class="py-3 px-4 border-b">Price (₱)</th>
                    <th class="py-3 px-4 border-b">Active</th>
                    <th class="py-3 px-4 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shippingRates as $rate)
                    <tr id="row-{{ $rate->id }}" class="border-b">
                        <td class="py-2 px-4">
                            <input type="text" value="{{ $rate->province }}" 
                                   class="border p-1 w-full rounded" id="province-{{ $rate->id }}">
                        </td>
                        <td class="py-2 px-4">
                            <input type="number" step="0.01" value="{{ $rate->price }}" 
                                   class="border p-1 w-full rounded" id="price-{{ $rate->id }}">
                        </td>
                        <td class="py-2 px-4">
                            <input type="checkbox" {{ $rate->is_active ? 'checked' : '' }} 
                                   id="active-{{ $rate->id }}">
                        </td>
                        <td class="py-2 px-4 text-center space-x-2">
                            <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" 
                                    onclick="updateRate({{ $rate->id }})">Save</button>
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" 
                                    onclick="deleteRate({{ $rate->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // PROVINCE BASE RATE FUNCTIONS (YOUR EXISTING CODE)
    function updateRate(id) {
        const province = document.getElementById(`province-${id}`).value;
        const price = document.getElementById(`price-${id}`).value;
        const is_active = document.getElementById(`active-${id}`).checked ? 1 : 0;

        fetch(`/shipping-settings/update/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ province, price, is_active })
        })
        .then(res => res.json())
        .then(data => alert(data.message))
        .catch(() => alert('Error updating shipping rate.'));
    }

    function deleteRate(id) {
        if (!confirm('Are you sure you want to delete this rate?')) return;

        fetch(`/shipping-settings/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById(`row-${id}`).remove();
            alert(data.message);
        })
        .catch(() => alert('Error deleting shipping rate.'));
    }

    // FIXED QUANTITY RATE FUNCTIONS (NEW)
    let tempFixedId = 0;

    function addNewFixedRate() {
        tempFixedId--;
        const tbody = document.getElementById('fixedRatesTable');
        
        // Remove "no data" row if exists
        const noDataRow = document.getElementById('no-fixed-data');
        if (noDataRow) {
            noDataRow.remove();
        }

        const newRow = document.createElement('tr');
        newRow.id = `fixed-row-${tempFixedId}`;
        newRow.className = 'border-b bg-yellow-50';
        newRow.innerHTML = `
            <td class="py-2 px-4">
                <div class="flex items-center gap-2">
                    <input type="number" value="1" class="border p-1 w-20 rounded" id="fixed-qty-from-${tempFixedId}" min="1">
                    <span class="text-gray-500">to</span>
                    <input type="number" value="1" class="border p-1 w-20 rounded" id="fixed-qty-to-${tempFixedId}" min="1">
                    <span class="text-sm text-gray-500">items</span>
                </div>
            </td>
            <td class="py-2 px-4">
                <input type="number" step="0.01" value="100" class="border p-1 w-full rounded" id="fixed-price-${tempFixedId}">
            </td>
            <td class="py-2 px-4">
                <input type="checkbox" checked id="fixed-active-${tempFixedId}">
            </td>
            <td class="py-2 px-4 text-center space-x-2">
                <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700" 
                        onclick="saveNewFixedRate(${tempFixedId})">Create</button>
                <button class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700" 
                        onclick="cancelNewFixedRate(${tempFixedId})">Cancel</button>
            </td>
        `;
        tbody.appendChild(newRow);
    }

    function saveNewFixedRate(tempId) {
        const qtyFrom = document.getElementById(`fixed-qty-from-${tempId}`).value;
        const qtyTo = document.getElementById(`fixed-qty-to-${tempId}`).value;
        const price = document.getElementById(`fixed-price-${tempId}`).value;

        if (parseInt(qtyTo) < parseInt(qtyFrom)) {
            alert('Quantity "to" must be greater than or equal to "from"');
            return;
        }

        fetch('/shipping-settings/fixed-rates/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity_from: qtyFrom,
                quantity_to: qtyTo,
                fixed_price: price
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(() => alert('Error adding fixed rate.'));
    }

    function cancelNewFixedRate(tempId) {
        const row = document.getElementById(`fixed-row-${tempId}`);
        row.remove();
        
        // Add back "no data" row if table is empty
        const tbody = document.getElementById('fixedRatesTable');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `
                <tr id="no-fixed-data">
                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                        No fixed rates configured. Click "Add Fixed Rate" to start.
                    </td>
                </tr>
            `;
        }
    }

    function updateFixedRate(id) {
        const qtyFrom = document.getElementById(`fixed-qty-from-${id}`).value;
        const qtyTo = document.getElementById(`fixed-qty-to-${id}`).value;
        const price = document.getElementById(`fixed-price-${id}`).value;
        const isActive = document.getElementById(`fixed-active-${id}`).checked ? 1 : 0;

        if (parseInt(qtyTo) < parseInt(qtyFrom)) {
            alert('Quantity "to" must be greater than or equal to "from"');
            return;
        }

        fetch(`/shipping-settings/fixed-rates/update/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity_from: qtyFrom,
                quantity_to: qtyTo,
                fixed_price: price,
                is_active: isActive
            })
        })
        .then(res => res.json())
        .then(data => alert(data.message))
        .catch(() => alert('Error updating fixed rate.'));
    }

    function deleteFixedRate(id) {
        if (!confirm('Are you sure you want to delete this fixed rate?')) return;

        fetch(`/shipping-settings/fixed-rates/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById(`fixed-row-${id}`).remove();
            alert(data.message);
            
            // Add back "no data" row if table is empty
            const tbody = document.getElementById('fixedRatesTable');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-fixed-data">
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                            No fixed rates configured. Click "Add Fixed Rate" to start.
                        </td>
                    </tr>
                `;
            }
        })
        .catch(() => alert('Error deleting fixed rate.'));
    }
</script>
@endsection