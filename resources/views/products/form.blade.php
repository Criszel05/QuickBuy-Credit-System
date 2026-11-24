@extends('layouts.quickbuy')

@section('title', $pageTitle . ' - QuickBuy')
@section('header', 'Product Management')

@section('content')

<div class="page-heading">
    <h1>{{ $pageTitle }}</h1>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        Back to Products
    </a>
</div>

<div class="card">

    <form method="POST"
          action="{{ $product->exists
              ? route('products.update', $product)
              : route('products.store') }}">

        @csrf

        @if($product->exists)
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Product Code *</label>
                <input type="text" name="product_code"
                       value="{{ old('product_code', $product->product_code) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name"
                       value="{{ old('name', $product->name) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Price (₱) *</label>
                <input type="number" name="price" step="0.01" min="0"
                       value="{{ old('price', $product->price) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Stock Quantity *</label>
                <input type="number" name="stock_quantity" min="0"
                       value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Reorder Level *</label>
                <input type="number" name="reorder_level" min="0"
                       value="{{ old('reorder_level', $product->reorder_level ?? 5) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Unit *</label>
                <input type="text" name="unit"
                       value="{{ old('unit', $product->unit ?? 'piece') }}"
                       required>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="is_active" required>
                    <option value="1"
                        @selected((string) old('is_active', $product->exists ? (int) $product->is_active : 1) === '1')>
                        Active
                    </option>
                    <option value="0"
                        @selected((string) old('is_active', $product->exists ? (int) $product->is_active : 1) === '0')>
                        Inactive
                    </option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        <button type="submit" class="btn">
            {{ $product->exists ? 'Update Product' : 'Save Product' }}
        </button>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection