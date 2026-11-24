
@extends('layouts.quickbuy')

@section('title', 'Inventory Management')
@section('header', 'Inventory Management')

@section('content')

<div class="page-heading">
    <div>
        <h1>Inventory</h1>
        <p class="muted">Monitor product stock and inventory movements.</p>
    </div>

    <a href="{{ route('inventory.create') }}" class="btn">
        + Update Stock
    </a>
</div>

<div class="form-grid">
    <div class="card">
        <p class="muted">Active Products</p>
        <h2>{{ number_format($totalProducts) }}</h2>
    </div>

    <div class="card">
        <p class="muted">Total Stock Units</p>
        <h2>{{ number_format($totalStock) }}</h2>
    </div>

    <div class="card">
        <p class="muted">Low Stock Products</p>
        <h2>{{ number_format($lowStockCount) }}</h2>
    </div>
</div>

<div class="card">
    <div class="page-heading">
        <h2>Product Stock Levels</h2>

        <div class="actions">
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-secondary">
                Low Stock
            </a>

            <a href="{{ route('inventory.history') }}" class="btn btn-secondary">
                Stock History
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('inventory.index') }}"
          style="display:flex; gap:10px; margin-bottom:20px;">
        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Search product name or code..."
        >

        <button type="submit" class="btn">Search</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->product_code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>{{ number_format($product->stock_quantity) }}</td>
                        <td>{{ number_format($product->reorder_level) }}</td>
                        <td>{{ $product->unit }}</td>
                        <td>
                            @if($product->stock_quantity <= $product->reorder_level)
                                <span class="badge badge-inactive">Low Stock</span>
                            @else
                                <span class="badge badge-active">In Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted" style="text-align:center;">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $products->links() }}
    </div>
</div>

<div class="card">
    <div class="page-heading">
        <h2>Recent Stock Movements</h2>
        <a href="{{ route('inventory.history') }}">View All History</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Movement</th>
                    <th>Quantity</th>
                    <th>Previous Stock</th>
                    <th>New Stock</th>
                    <th>Recorded By</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentMovements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $movement->product->name ?? 'Deleted Product' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $movement->movement_type)) }}</td>
                        <td>{{ number_format($movement->quantity) }}</td>
                        <td>{{ number_format($movement->previous_stock) }}</td>
                        <td>{{ number_format($movement->new_stock) }}</td>
                        <td>{{ $movement->user->name ?? 'Unknown' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted" style="text-align:center;">
                            No stock movements recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
