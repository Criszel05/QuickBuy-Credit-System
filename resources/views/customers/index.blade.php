@extends('layouts.quickbuy')

@section('title', 'Customers')
@section('header', 'Customer Management')

@section('content')

<div class="page-heading">
    <div>
        <h1>Customers</h1>
        <p class="muted">Manage customer information and credit limits.</p>
    </div>

    <a href="{{ route('customers.create') }}" class="btn">
        + Add Customer
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;">
    <div class="card">
        <p class="muted">Active Customers</p>
        <h2>{{ $totalCustomers }}</h2>
    </div>

    <div class="card">
        <p class="muted">Inactive Customers</p>
        <h2>{{ $inactiveCustomers }}</h2>
    </div>

    <div class="card">
        <p class="muted">Total Credit Limits</p>
        <h2>₱{{ number_format($totalCreditLimit, 2) }}</h2>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('customers.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;">

        <div style="flex:1;min-width:200px;">
            <input type="text" name="search"
                   value="{{ $search }}"
                   placeholder="Search customer code, name, or phone">
        </div>

        <button type="submit" class="btn">Search</button>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <h3>Customer Records</h3>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Credit Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->customer_code }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone ?: 'N/A' }}</td>
                        <td>₱{{ number_format($customer->credit_limit, 2) }}</td>

                        <td>
                            @if($customer->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <div class="actions">
                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="btn btn-small">Edit</a>

                                @if($customer->is_active)
                                    <form method="POST"
                                          action="{{ route('customers.destroy', $customer) }}"
                                          onsubmit="return confirm('Deactivate this customer?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-small">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('customers.activate', $customer) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-small">
                                            Activate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">
                            No customer records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $customers->links() }}
    </div>
</div>

@endsection
