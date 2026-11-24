
@extends('layouts.quickbuy')

@section('title', 'Update Inventory')
@section('header', 'Update Inventory')

@section('content')

<div class="page-heading">
    <div>
        <h1>Stock Update</h1>
        <p class="muted">Record stock-in, stock-out, or physical inventory adjustment.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card" style="max-width:750px;">

    <form method="POST" action="{{ route('inventory.store') }}">
        @csrf

        <div class="form-group">
            <label for="product_id">Select Product</label>

            <select name="product_id" id="product_id" required>
                <option value="">-- Select Product --</option>

                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->product_code }} - {{ $product->name }}
                        (Current Stock: {{ $product->stock_quantity }} {{ $product->unit }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="movement_type">Movement Type</label>

            <select name="movement_type" id="movement_type" required>
                <option value="">-- Select Movement --</option>
                <option value="stock_in" {{ old('movement_type') == 'stock_in' ? 'selected' : '' }}>
                    Stock In - Add Stock
                </option>
                <option value="stock_out" {{ old('movement_type') == 'stock_out' ? 'selected' : '' }}>
                    Stock Out - Remove Stock
                </option>
                <option value="adjustment" {{ old('movement_type') == 'adjustment' ? 'selected' : '' }}>
                    Stock Adjustment - Set Actual Count
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity" id="quantity_label">Quantity</label>

            <input
                type="number"
                name="quantity"
                id="quantity"
                min="1"
                step="1"
                value="{{ old('quantity') }}"
                required
            >

            <small class="muted" id="quantity_help">
                Enter the quantity to add or remove.
            </small>
        </div>

        <div class="form-group">
            <label for="remarks">Remarks / Reason</label>

            <textarea
                name="remarks"
                id="remarks"
                rows="4"
                placeholder="Enter reason for this stock movement..."
            >{{ old('remarks') }}</textarea>
        </div>

        <button type="submit" class="btn">
            Save Stock Movement
        </button>

        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>

<script>
    const movementType = document.getElementById('movement_type');
    const quantityLabel = document.getElementById('quantity_label');
    const quantityHelp = document.getElementById('quantity_help');

    function updateQuantityLabel() {
        if (movementType.value === 'adjustment') {
            quantityLabel.textContent = 'Actual Counted Stock';
            quantityHelp.textContent =
                'Enter the actual quantity counted during physical inventory.';
        } else if (movementType.value === 'stock_out') {
            quantityLabel.textContent = 'Quantity to Remove';
            quantityHelp.textContent =
                'Enter how many units will be removed from inventory.';
        } else {
            quantityLabel.textContent = 'Quantity to Add';
            quantityHelp.textContent =
                'Enter how many units will be added to inventory.';
        }
    }

    movementType.addEventListener('change', updateQuantityLabel);
    updateQuantityLabel();
</script>

@endsection
