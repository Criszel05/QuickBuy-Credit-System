
@extends('layouts.quickbuy')

@section('title', 'Sales History')

@section('content')
<div class="page-header">
    <div>
        <h1>Sales History</h1>
        <p>View and monitor all recorded transactions.</p>
    </div>
    <a href="{{ route('pos.create') }}" class="btn btn-primary">+ New Sale</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Completed Cash Sales</span>
        <h2>₱{{ number_format($totalSales, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Total Transactions</span>
        <h2>{{ $totalTransactions }}</h2>
    </div>
    <div class="stat-card">
        <span>Credit Sales</span>
        <h2>₱{{ number_format($creditSales, 2) }}</h2>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('sales.index') }}" class="search-form">
        <input type="text" name="search" value="{{ $search }}"
            placeholder="Search invoice or customer...">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Clear</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                        <td>{{ $sale->user?->name ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge {{ $sale->payment_method === 'credit' ? 'status-warning' : 'status-success' }}">
                                {{ ucfirst($sale->payment_method) }}
                            </span>
                        </td>
                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No sales records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $sales->links() }}
    </div>
</div>
@endsection
