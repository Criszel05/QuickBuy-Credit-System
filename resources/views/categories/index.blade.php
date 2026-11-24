@extends('layouts.quickbuy')

@section('title', 'Categories - QuickBuy')
@section('header', 'Category Management')

@section('content')

<div class="page-heading">
    <h1>Categories</h1>
    <a href="{{ route('categories.create') }}" class="btn">+ Add Category</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Number of Products</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->description ?: 'No description' }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('categories.edit', $category) }}"
                                   class="btn btn-small">Edit</a>

                                <form method="POST"
                                      action="{{ route('categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete this category? Products under it will remain uncategorized.')">
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
                        <td colspan="4" style="text-align:center;padding:25px;">
                            No categories found. Click Add Category to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $categories->links() }}
    </div>
</div>

@endsection