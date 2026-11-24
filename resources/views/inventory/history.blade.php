
@extends('layouts.quickbuy')

@section('title', 'Stock History')
@section('header', 'Stock Movement History')

@section('content')

<div class="page-heading">
    <div>
        <h1>Stock History</h1>
        <p class="muted">View all recorded inventory movements.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card">

    <form method="GET" action="{{ route('inventory.history') }}"
          class="form-grid" style="margin-bottom:20px;">

        <div class="form-group">
            <label>Search Product</label>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Product name or code..."
            >
        </div>

        <div class="form-group">
            <label>Movement Type</label>

            <select name="type">
                <option value="">All Movements</option>
                <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>
                    Stock In
                </option>
                <option value="stock_out" {{ request('type') == 'stock_out' ? 'selected' : '' }}>
                    Stock Out
                </option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>
                    Adjustment
                </option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn">Filter History</button>
            <a href="{{ route('inventory.history') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Movement</th>
                    <th>Quantity / Difference</th>
                    <th>Previous Stock</th>
                    <th>New Stock</th>
                    <th>User</th>
                    <th>Remarks</th>
                </tr>
            </thead>

            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $movement->product->name ?? 'Deleted Product' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $movement->movement_type)) }}</td>
                        <td>{{ number_format($movement->quantity) }}</td>
                        <td>{{ number_format($movement->previous_stock) }}</td>
                        <td>{{ number_format($movement->new_stock) }}</td>
                        <td>{{ $movement->user->name ?? 'Unknown' }}</td>
                        <td>{{ $movement->remarks ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="muted" style="text-align:center;">
                            No stock movement history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $movements->links() }}
    </div>

</div>

@endsection
