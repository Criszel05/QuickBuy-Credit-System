
@extends('layouts.quickbuy')

@section('title', 'Point of Sale')

@section('content')
<div class="page-header">
    <div>
        <h1>Point of Sale</h1>
        <p>Create a new customer transaction.</p>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Sales History</a>
</div>

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Please check the following:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('pos.store') }}" method="POST" id="posForm">
    @csrf

    <div class="card">
        <h3>Transaction Details</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id" id="customer_id">
                    <option value="">Walk-in Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->customer_code }} - {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Credit / Utang</option>
                </select>
            </div>

            <div class="form-group">
                <label>Discount (₱)</label>
                <input type="number" name="discount" id="discount"
                    min="0" step="0.01" value="{{ old('discount', 0) }}">
            </div>

            <div class="form-group" id="cashGroup">
                <label>Cash Received (₱)</label>
                <input type="number" name="amount_paid" id="amount_paid"
                    min="0" step="0.01" value="{{ old('amount_paid', 0) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <h3>Products</h3>
            <button type="button" class="btn btn-secondary" id="addRow">+ Add Product</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Available Stock</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Line Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartBody"></tbody>
            </table>
        </div>

        <div class="pos-total">
            <div>
                <span>Subtotal</span>
                <strong id="subtotalDisplay">₱0.00</strong>
            </div>
            <div>
                <span>Discount</span>
                <strong id="discountDisplay">₱0.00</strong>
            </div>
            <div class="grand-total">
                <span>Total Amount</span>
                <strong id="totalDisplay">₱0.00</strong>
            </div>
            <div id="changeRow">
                <span>Change</span>
                <strong id="changeDisplay">₱0.00</strong>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-large">
            Complete Sale
        </button>
    </div>
</form>

<script>
@php
    $posProducts = $products->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'code' => $p->product_code,
            'price' => (float) $p->price,
            'stock' => (int) $p->stock_quantity,
        ];
    })->values();
@endphp

<script>
const products = {{ \Illuminate\Support\Js::from($posProducts) }};

const cartBody = document.getElementById('cartBody');
let rowIndex = 0;

function money(value) {
    return '₱' + Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function addRow(selectedId = '') {
    const row = document.createElement('tr');
    const index = rowIndex++;

    let options = '<option value="">Select Product</option>';

    products.forEach(product => {
        const selected = String(product.id) === String(selectedId) ? 'selected' : '';
        options += `<option value="${product.id}" ${selected}>
            ${product.code} - ${product.name}
        </option>`;
    });

    row.innerHTML = `
        <td>
            <select name="items[${index}][product_id]" class="product-select" required>
                ${options}
            </select>
        </td>
        <td class="stock-cell">-</td>
        <td class="price-cell">₱0.00</td>
        <td>
            <input type="number" name="items[${index}][quantity]"
                class="quantity-input" min="1" value="1" required>
        </td>
        <td class="line-total">₱0.00</td>
        <td>
            <button type="button" class="btn btn-danger remove-row">Remove</button>
        </td>
    `;

    cartBody.appendChild(row);
    updateRow(row);
    calculateTotals();
}

function updateRow(row) {
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const product = products.find(p => String(p.id) === select.value);
    const quantity = Math.max(1, parseInt(quantityInput.value || 1));

    if (!product) {
        row.querySelector('.stock-cell').textContent = '-';
        row.querySelector('.price-cell').textContent = money(0);
        row.querySelector('.line-total').textContent = money(0);
        row.dataset.lineTotal = 0;
        calculateTotals();
        return;
    }

    row.querySelector('.stock-cell').textContent = product.stock;
    row.querySelector('.price-cell').textContent = money(product.price);
    row.querySelector('.line-total').textContent = money(product.price * quantity);
    row.dataset.lineTotal = product.price * quantity;

    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;

    document.querySelectorAll('#cartBody tr').forEach(row => {
        subtotal += Number(row.dataset.lineTotal || 0);
    });

    const discount = Math.max(0, Number(document.getElementById('discount').value || 0));
    const total = Math.max(0, subtotal - discount);
    const paid = Math.max(0, Number(document.getElementById('amount_paid').value || 0));
    const isCash = document.getElementById('payment_method').value === 'cash';

    document.getElementById('subtotalDisplay').textContent = money(subtotal);
    document.getElementById('discountDisplay').textContent = money(discount);
    document.getElementById('totalDisplay').textContent = money(total);
    document.getElementById('changeDisplay').textContent = money(isCash ? Math.max(0, paid - total) : 0);

    document.getElementById('cashGroup').style.display = isCash ? '' : 'none';
    document.getElementById('changeRow').style.display = isCash ? '' : 'none';
}

document.getElementById('addRow').addEventListener('click', () => addRow());

cartBody.addEventListener('change', event => {
    if (event.target.matches('.product-select')) {
        updateRow(event.target.closest('tr'));
    }
});

cartBody.addEventListener('input', event => {
    if (event.target.matches('.quantity-input')) {
        updateRow(event.target.closest('tr'));
    }
});

cartBody.addEventListener('click', event => {
    if (event.target.matches('.remove-row')) {
        event.target.closest('tr').remove();
        calculateTotals();
    }
});

document.getElementById('discount').addEventListener('input', calculateTotals);
document.getElementById('amount_paid').addEventListener('input', calculateTotals);
document.getElementById('payment_method').addEventListener('change', calculateTotals);

document.getElementById('posForm').addEventListener('submit', event => {
    if (cartBody.querySelectorAll('tr').length === 0) {
        event.preventDefault();
        alert('Please add at least one product.');
        return;
    }

    if (document.getElementById('payment_method').value === 'credit'
        && !document.getElementById('customer_id').value) {
        event.preventDefault();
        alert('Please select a customer for a credit sale.');
    }
});

addRow();
</script>
@endsection

