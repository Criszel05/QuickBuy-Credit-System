@extends('layouts.quickbuy')

@section('title', 'Products - QuickBuy')
@section('header', 'Product Management')

@section('content')

<div class="page-heading">
    <h1>Products</h1>
    <a href="{{ route('products.create') }}" class="btn">+ Add Product</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('products.index') }}"
          style="display:flex;gap:10px;max-width:550px;">
        <input type="text" name="search"
               value="{{ $search }}"
               placeholder="Search product name or product code">

        <button class="btn" type="submit">Search</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Clear</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->product_code }}</td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <span class="muted">{{ $product->unit }}</span>
                        </td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->reorder_level }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="btn btn-small">Edit</a>

                                <form method="POST"
                                      action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('Are you sure you want to delete or deactivate this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-small">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:25px;">
                            No products found. Click Add Product to create one.
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