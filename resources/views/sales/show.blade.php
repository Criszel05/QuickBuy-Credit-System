
@extends('layouts.quickbuy')

@section('title', 'Sales Invoice')

@section('content')
<div class="page-header no-print">
    <div>
        <h1>Sales Invoice</h1>
        <p>Transaction details and receipt.</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales</a>
    </div>
</div>

<div class="card invoice-card">
    <div class="invoice-header">
        <div>
            <h1>QUICKBUY</h1>
            <p>Sales Receipt</p>
        </div>
        <div class="invoice-info">
            <strong>{{ $sale->invoice_number }}</strong>
            <p>{{ $sale->created_at->format('F d, Y h:i A') }}</p>
        </div>
    </div>

    <hr>

    <div class="invoice-customer">
        <p><strong>Customer:</strong> {{ $sale->customer?->name ?? 'Walk-in Customer' }}</p>
        <p><strong>Cashier:</strong> {{ $sale->user?->name ?? 'N/A' }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($sale->status) }}</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                        <td>₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-totals">
        <p><span>Subtotal</span><strong>₱{{ number_format($sale->subtotal, 2) }}</strong></p>
        <p><span>Discount</span><strong>₱{{ number_format($sale->discount, 2) }}</strong></p>
        <p class="invoice-grand-total">
            <span>Total</span>
            <strong>₱{{ number_format($sale->total_amount, 2) }}</strong>
        </p>

        @if ($sale->payment_method === 'cash')
            <p><span>Cash Received</span><strong>₱{{ number_format($sale->amount_paid, 2) }}</strong></p>
            <p><span>Change</span><strong>₱{{ number_format($sale->change_amount, 2) }}</strong></p>
        @else
            <p><span>Credit Balance</span><strong>₱{{ number_format($sale->total_amount, 2) }}</strong></p>
        @endif
    </div>

    <div class="invoice-footer">
        <p>Thank you for shopping at QuickBuy!</p>
    </div>
</div>

<style>
.invoice-card { max-width: 850px; margin: 0 auto; padding: 30px; }
.invoice-header { display: flex; justify-content: space-between; gap: 20px; }
.invoice-header h1 { margin: 0; color: #4b2e20; }
.invoice-info { text-align: right; }
.invoice-customer { margin: 25px 0; }
.invoice-customer p { margin: 7px 0; }
.invoice-totals { max-width: 350px; margin: 25px 0 0 auto; }
.invoice-totals p { display: flex; justify-content: space-between; gap: 20px; }
.invoice-grand-total { border-top: 2px solid #8b6246; padding-top: 12px; font-size: 20px; }
.invoice-footer { text-align: center; margin-top: 35px; color: #8b6246; }
@media print {
    .no-print, aside, nav, header, footer { display: none !important; }
    body { background: white !important; }
    .invoice-card { box-shadow: none !important; border: none !important; }
}
</style>
@endsection
