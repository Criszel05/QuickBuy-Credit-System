
@extends('layouts.quickbuy')

@section('title', 'Payment History')

@section('content')
<div class="page-header">
    <div>
        <h1>Payment History</h1>
        <p>All customer payments recorded in QuickBuy.</p>
    </div>
    <a href="{{ route('credits.index') }}" class="btn btn-primary">View Credit Records</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Payments Collected</span>
        <h2>₱{{ number_format($totalPayments, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Total Payment Receipts</span>
        <h2>{{ $totalReceipts }}</h2>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('payments.index') }}" class="search-form">
        <input type="text" name="search" value="{{ $search }}"
            placeholder="Search receipt, credit number, or customer...">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Clear</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Receipt Number</th>
                    <th>Customer</th>
                    <th>Credit Number</th>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->credit?->customer?->name ?? 'N/A' }}</td>
                        <td>
                            @if ($payment->credit)
                                <a href="{{ route('credits.show', $payment->credit) }}">
                                    {{ $payment->credit->credit_number }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->user?->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No payment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $payments->links() }}
    </div>
</div>
@endsection