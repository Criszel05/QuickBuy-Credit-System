@extends('layouts.quickbuy')

@section('title', $pageTitle . ' - QuickBuy')
@section('header', 'Category Management')

@section('content')

<div class="page-heading">
    <h1>{{ $pageTitle }}</h1>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        Back to Categories
    </a>
</div>

<div class="card">

    <form method="POST"
          action="{{ $category->exists
              ? route('categories.update', $category)
              : route('categories.store') }}">

        @csrf

        @if($category->exists)
            @method('PUT')
        @endif

        <div class="form-group">
            <label>Category Name *</label>
            <input type="text" name="name"
                   value="{{ old('name', $category->name) }}"
                   required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea>
        </div>

        <button type="submit" class="btn">
            {{ $category->exists ? 'Update Category' : 'Save Category' }}
        </button>

        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection