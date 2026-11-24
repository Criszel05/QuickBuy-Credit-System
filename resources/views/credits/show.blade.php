
@extends('layouts.quickbuy')

@section('title', 'Credit Details')

@section('content')
<div class="page-header">
    <div>
        <h1>Credit Details</h1>
        <p>{{ $credit->credit_number }}</p>
    </div>
    <a href="{{ route('credits.index') }}" class="btn btn-secondary">Back to Credits</a>
</div>

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Please correct the following:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Credit</span>
        <h2>₱{{ number_format($credit->total_amount, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Total Paid</span>
        <h2>₱{{ number_format($credit->paid_amount, 2) }}</h2>
    </div>
    <div class="stat-card">
        <span>Remaining Balance</span>
        <h2>₱{{ number_format($credit->balance, 2) }}</h2>
    </div>
</div>

<div class="card">
    <h3>Customer Information</h3>
    <div class="form-grid">
        <div>
            <p><strong>Customer:</strong> {{ $credit->customer?->name ?? 'Unknown' }}</p>
            <p><strong>Customer Code:</strong> {{ $credit->customer?->customer_code ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $credit->customer?->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <p><strong>Credit Date:</strong> {{ $credit->credit_date?->format('M d, Y') ?? 'N/A' }}</p>
            <p><strong>Due Date:</strong> {{ $credit->due_date?->format('M d, Y') ?? 'Not set' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($credit->status) }}</p>
        </div>
    </div>
    @if ($credit->notes)
        <p style="margin-top:15px;"><strong>Notes:</strong> {{ $credit->notes }}</p>
    @endif
</div>

@if ((float) $credit->balance > 0)
<div class="card">
    <h3>Record Customer Payment</h3>

    <form action="{{ route('payments.store', $credit) }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Payment Amount (₱)</label>
                <input type="number" name="amount" min="0.01"
                    max="{{ $credit->balance }}" step="0.01"
                    value="{{ old('amount') }}" required>
                <small>Remaining balance: ₱{{ number_format($credit->balance, 2) }}</small>
            </div>

            <div class="form-group">
                <label>Payment Date</label>
                <input type="date" name="payment_date"
                    value="{{ old('payment_date', now()->toDateString()) }}" required>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"
            onclick="return confirm('Record this payment?')">
            Save Payment
        </button>
    </form>
</div>
@else
<div class="alert alert-success">
    This credit has been fully paid. No remaining balance.
</div>
@endif

<div class="card">
    <h3>Payment History</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Receipt Number</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($credit->payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->user?->name ?? 'N/A' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No payments recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
