
@extends('layouts.quickbuy')

@section('title', 'Credit / Utang')

@section('content')
<div class="page-header">
    <div>
        <h1>Credit / Utang Records</h1>
        <p>Monitor customer balances and outstanding credit.</p>
    </div>
    <a href="{{ route('payments.index') }}" class="btn btn-primary">Payment History</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Credit</span>
        <h2>₱{{ number_format($totalCredit, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Total Paid</span>
        <h2>₱{{ number_format($totalPaid, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Remaining Balance</span>
        <h2>₱{{ number_format($totalBalance, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Unpaid / Partial Records</span>
        <h2>{{ $unpaidCount }}</h2>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('credits.index') }}" class="search-form">
        <input type="text" name="search" value="{{ $search }}"
            placeholder="Search credit number or customer...">

        <select name="status" style="padding:10px;border:1px solid #d8c7b8;border-radius:6px;">
            <option value="">All Status</option>
            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('credits.index') }}" class="btn btn-secondary">Clear</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Credit Number</th>
                    <th>Customer</th>
                    <th>Credit Date</th>
                    <th>Total Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($credits as $credit)
                    <tr>
                        <td>{{ $credit->credit_number }}</td>
                        <td>{{ $credit->customer?->name ?? 'Unknown Customer' }}</td>
                        <td>{{ $credit->credit_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>₱{{ number_format($credit->total_amount, 2) }}</td>
                        <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                        <td><strong>₱{{ number_format($credit->balance, 2) }}</strong></td>
                        <td>
                            @if ($credit->status === 'paid')
                                <span class="status-badge status-success">Paid</span>
                            @elseif ($credit->status === 'partial')
                                <span class="status-badge status-warning">Partial</span>
                            @else
                                <span class="status-badge status-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('credits.show', $credit) }}"
                               class="btn btn-secondary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">No credit records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $credits->links() }}
    </div>
</div>
@endsection
