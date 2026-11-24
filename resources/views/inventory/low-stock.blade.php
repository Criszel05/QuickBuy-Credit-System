
@extends('layouts.quickbuy')

@section('title', 'Low Stock Products')
@section('header', 'Low Stock Monitoring')

@section('content')

<div class="page-heading">
    <div>
        <h1>Low Stock Products</h1>
        <p class="muted">
            Products whose current quantity has reached or fallen below their reorder level.
        </p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Unit</th>
                    <th>Stock Status</th>
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
                            <span class="badge badge-inactive">Low Stock</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted" style="text-align:center;">
                            No low-stock products. Inventory levels are good.
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

@endsection