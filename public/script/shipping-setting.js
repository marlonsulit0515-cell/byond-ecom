// Get CSRF token from meta tag
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found. Make sure you have <meta name="csrf-token"> in your layout.');
        return '';
    }
    return token.content;
}

// Helper function for fetch requests
function makeRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    };
    
    return fetch(url, { ...defaultOptions, ...options })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'An error occurred');
            }
            return data;
        });
}

// ============================================
// PROVINCE BASE RATES FUNCTIONS
// ============================================

function updateRate(id) {
    const province = document.getElementById(`province-${id}`).value.trim();
    const price = document.getElementById(`price-${id}`).value;
    const is_active = document.getElementById(`active-${id}`).checked ? 1 : 0;

    // Validation
    if (!province) {
        alert('Province name cannot be empty');
        return;
    }
    if (!price || parseFloat(price) < 0) {
        alert('Please enter a valid price');
        return;
    }

    // Disable button during request
    const button = event.target;
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Saving...';

    makeRequest(`/shipping-settings/update/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({ province, price, is_active })
    })
    .then(data => {
        alert(data.message || 'Shipping rate updated successfully!');
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

function deleteRate(id) {
    if (!confirm('Are you sure you want to delete this rate?')) return;

    const button = event.target;
    button.disabled = true;
    button.textContent = 'Deleting...';

    makeRequest(`/shipping-settings/delete/${id}`, {
        method: 'DELETE'
    })
    .then(data => {
        document.getElementById(`row-${id}`).remove();
        alert(data.message || 'Shipping rate deleted successfully!');
    })
    .catch(error => {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.textContent = 'Delete';
    });
}

// ============================================
// FIXED QUANTITY RATE FUNCTIONS
// ============================================

let tempFixedId = -1000;

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
    newRow.className = 'bg-yellow-50';
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
            <input type="number" step="0.01" value="100" class="border p-1 w-full rounded" id="fixed-price-${tempFixedId}" min="0">
        </td>
        <td class="py-2 px-4 text-center">
            <input type="checkbox" checked id="fixed-active-${tempFixedId}">
        </td>
        <td class="py-2 px-4 text-center">
            <div class="flex items-center justify-center gap-2">
                <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700" 
                        onclick="saveNewFixedRate(${tempFixedId})">Create</button>
                <button class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700" 
                        onclick="cancelNewFixedRate(${tempFixedId})">Cancel</button>
            </div>
        </td>
    `;
    tbody.appendChild(newRow);
}

function saveNewFixedRate(tempId) {
    const qtyFrom = parseInt(document.getElementById(`fixed-qty-from-${tempId}`).value);
    const qtyTo = parseInt(document.getElementById(`fixed-qty-to-${tempId}`).value);
    const price = parseFloat(document.getElementById(`fixed-price-${tempId}`).value);

    // Validation
    if (!qtyFrom || qtyFrom < 1) {
        alert('Quantity "from" must be at least 1');
        return;
    }
    if (!qtyTo || qtyTo < 1) {
        alert('Quantity "to" must be at least 1');
        return;
    }
    if (qtyTo < qtyFrom) {
        alert('Quantity "to" must be greater than or equal to "from"');
        return;
    }
    if (!price || price < 0) {
        alert('Please enter a valid price');
        return;
    }

    const button = event.target;
    button.disabled = true;
    button.textContent = 'Creating...';

    makeRequest('/shipping-settings/fixed-rates/add', {
        method: 'POST',
        body: JSON.stringify({
            quantity_from: qtyFrom,
            quantity_to: qtyTo,
            fixed_price: price
        })
    })
    .then(data => {
        alert(data.message || 'Fixed rate added successfully!');
        // Reload to show the new rate with proper ID
        location.reload();
    })
    .catch(error => {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.textContent = 'Create';
    });
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
    const qtyFrom = parseInt(document.getElementById(`fixed-qty-from-${id}`).value);
    const qtyTo = parseInt(document.getElementById(`fixed-qty-to-${id}`).value);
    const price = parseFloat(document.getElementById(`fixed-price-${id}`).value);
    const isActive = document.getElementById(`fixed-active-${id}`).checked ? 1 : 0;

    // Validation
    if (!qtyFrom || qtyFrom < 1) {
        alert('Quantity "from" must be at least 1');
        return;
    }
    if (!qtyTo || qtyTo < 1) {
        alert('Quantity "to" must be at least 1');
        return;
    }
    if (qtyTo < qtyFrom) {
        alert('Quantity "to" must be greater than or equal to "from"');
        return;
    }
    if (!price || price < 0) {
        alert('Please enter a valid price');
        return;
    }

    const button = event.target;
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Saving...';

    makeRequest(`/shipping-settings/fixed-rates/update/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({
            quantity_from: qtyFrom,
            quantity_to: qtyTo,
            fixed_price: price,
            is_active: isActive
        })
    })
    .then(data => {
        alert(data.message || 'Fixed rate updated successfully!');
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

function deleteFixedRate(id) {
    if (!confirm('Are you sure you want to delete this fixed rate?')) return;

    const button = event.target;
    button.disabled = true;
    button.textContent = 'Deleting...';

    makeRequest(`/shipping-settings/fixed-rates/delete/${id}`, {
        method: 'DELETE'
    })
    .then(data => {
        document.getElementById(`fixed-row-${id}`).remove();
        alert(data.message || 'Fixed rate deleted successfully!');
        
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
    .catch(error => {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.textContent = 'Delete';
    });
}