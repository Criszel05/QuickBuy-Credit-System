
@extends('layouts.quickbuy')

@section('title', $customer->exists ? 'Edit Customer' : 'Add Customer')
@section('header', 'Customer Management')

@section('content')

<div class="page-heading">
    <div>
        <h1>{{ $customer->exists ? 'Edit Customer' : 'Add Customer' }}</h1>
        <p class="muted">Enter the customer's information below.</p>
    </div>

    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
        Back to Customers
    </a>
</div>

<div class="card">

    <form method="POST"
          action="{{ $customer->exists
              ? route('customers.update', $customer)
              : route('customers.store') }}">

        @csrf

        @if($customer->exists)
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Customer Code *</label>
                <input type="text"
                       name="customer_code"
                       value="{{ old('customer_code', $customer->customer_code) }}"
                       placeholder="Example: CUST-001"
                       required>
            </div>

            <div class="form-group">
                <label>Customer Name *</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $customer->name) }}"
                       placeholder="Enter customer name"
                       required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone', $customer->phone) }}"
                       placeholder="Enter phone number">
            </div>

            <div class="form-group">
                <label>Credit Limit (₱) *</label>
                <input type="number"
                       name="credit_limit"
                       value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}"
                       min="0"
                       step="0.01"
                       required>
            </div>

        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address"
                      rows="3"
                      placeholder="Enter customer address">{{ old('address', $customer->address) }}</textarea>
        </div>

        <button type="submit" class="btn">
            {{ $customer->exists ? 'Update Customer' : 'Save Customer' }}
        </button>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection
