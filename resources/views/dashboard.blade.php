
@extends('layouts.quickbuy')

@section('title', 'Dashboard')

@section('content')

<style>
    .dashboard-welcome {
        background: #4b2e20;
        color: white;
        padding: 28px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .dashboard-welcome h1 {
        font-size: 25px;
        margin-bottom: 8px;
    }

    .dashboard-welcome p {
        color: #ead9ca;
        line-height: 1.5;
    }

    .dashboard-section-title {
        font-size: 20px;
        margin: 28px 0 16px;
        color: #4b2e20;
    }

    .dashboard-stats,
    .dashboard-modules {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .dashboard-stat,
    .dashboard-module {
        background: white;
        border: 1px solid #eee2d6;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        min-width: 0;
    }

    .dashboard-stat {
        border-left: 5px solid #8b6246;
    }

    .dashboard-stat p {
        color: #777;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .dashboard-stat h2 {
        font-size: 25px;
        overflow-wrap: anywhere;
    }

    .dashboard-module {
        display: block;
        color: #4b2e20;
        text-decoration: none;
        transition: .2s;
    }

    .dashboard-module:hover {
        transform: translateY(-3px);
        border-color: #8b6246;
        box-shadow: 0 5px 14px rgba(75,46,32,.10);
    }

    .dashboard-module .module-icon {
        font-size: 29px;
        margin-bottom: 13px;
    }

    .dashboard-module h3 {
        font-size: 16px;
        margin-bottom: 9px;
    }

    .dashboard-module p {
        color: #777;
        font-size: 13px;
        line-height: 1.5;
        min-height: 38px;
    }

    .dashboard-module span {
        display: inline-block;
        color: #8b6246;
        font-weight: bold;
        font-size: 13px;
        margin-top: 13px;
    }

    .dashboard-table-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        overflow-x: auto;
        border: 1px solid #eee2d6;
        margin-bottom: 20px;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
        font-size: 13px;
    }

    .dashboard-table th,
    .dashboard-table td {
        padding: 13px;
        border-bottom: 1px solid #eee2d6;
        text-align: left;
    }

    .dashboard-table th {
        background: #f7f1e8;
    }

    .dashboard-empty {
        text-align: center !important;
        color: #888;
        padding: 22px !important;
    }

    @media(max-width: 1000px) {
        .dashboard-stats,
        .dashboard-modules {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 600px) {
        .dashboard-stats,
        .dashboard-modules {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-welcome">
    <h1>Welcome, {{ Auth::user()->name }}!</h1>
    <p>
        Welcome to QuickBuy Management System.
        Manage your sales, inventory, customers, employees,
        and company assets in one place.
    </p>
</div>

<h2 class="dashboard-section-title">Business Overview</h2>

<div class="dashboard-stats">
    <div class="dashboard-stat">
        <p>Total Sales Today</p>
        <h2>₱{{ number_format($totalSalesToday, 2) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Active Products</p>
        <h2>{{ number_format($totalProducts) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Outstanding Credits</p>
        <h2>₱{{ number_format($pendingCredits, 2) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Total Assets</p>
        <h2>{{ number_format($totalAssets) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Active Customers</p>
        <h2>{{ number_format($totalCustomers) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Active Employees</p>
        <h2>{{ number_format($totalEmployees) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Low Stock Products</p>
        <h2>{{ number_format($lowStockProducts) }}</h2>
    </div>

    <div class="dashboard-stat">
        <p>Overdue Credits</p>
        <h2>{{ number_format($overdueCredits) }}</h2>
    </div>
</div>

<h2 class="dashboard-section-title">Business Management</h2>

<div class="dashboard-modules">

    <a href="{{ route('pos.create') }}" class="dashboard-module">
        <div class="module-icon">🛒</div>
        <h3>Sales / POS</h3>
        <p>Process customer purchases and create sales transactions.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('sales.index') }}" class="dashboard-module">
        <div class="module-icon">🧾</div>
        <h3>Sales History</h3>
        <p>View recorded sales and transaction details.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('products.index') }}" class="dashboard-module">
        <div class="module-icon">📦</div>
        <h3>Products</h3>
        <p>Manage product names, prices, and stock quantities.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('categories.index') }}" class="dashboard-module">
        <div class="module-icon">📁</div>
        <h3>Categories</h3>
        <p>Organize products into categories.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('inventory.index') }}" class="dashboard-module">
        <div class="module-icon">📊</div>
        <h3>Inventory</h3>
        <p>Monitor stock levels and record stock movements.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('customers.index') }}" class="dashboard-module">
        <div class="module-icon">👥</div>
        <h3>Customers</h3>
        <p>Manage customer information and credit limits.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('credits.index') }}" class="dashboard-module">
        <div class="module-icon">💳</div>
        <h3>Credit / Utang</h3>
        <p>Monitor customer balances and outstanding credits.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('payments.index') }}" class="dashboard-module">
        <div class="module-icon">💰</div>
        <h3>Payments</h3>
        <p>View customer payments and payment history.</p>
        <span>Open Module →</span>
    </a>

</div>

<h2 class="dashboard-section-title">Asset Management</h2>

<div class="dashboard-modules">

    <a href="{{ route('assets.index') }}" class="dashboard-module">
        <div class="module-icon">💻</div>
        <h3>Asset Records</h3>
        <p>Manage company equipment and asset information.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('employees.index') }}" class="dashboard-module">
        <div class="module-icon">👨‍💼</div>
        <h3>Employees</h3>
        <p>Manage employee information and employment status.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('asset-assignments.index') }}" class="dashboard-module">
        <div class="module-icon">🔄</div>
        <h3>Asset Assignment & Transfer</h3>
        <p>Assign company assets to employees and record transfers.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('maintenance.index') }}" class="dashboard-module">
        <div class="module-icon">🛠️</div>
        <h3>Maintenance</h3>
        <p>Report asset problems and monitor repair activities.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('software-licenses.index') }}" class="dashboard-module">
        <div class="module-icon">💿</div>
        <h3>Software Licenses</h3>
        <p>Manage software licenses and asset installations.</p>
        <span>Open Module →</span>
    </a>

    <a href="{{ route('asset-disposals.index') }}" class="dashboard-module">
        <div class="module-icon">🗑️</div>
        <h3>Asset Disposal</h3>
        <p>Record retired assets and their disposal information.</p>
        <span>Open Module →</span>
    </a>

</div>

<h2 class="dashboard-section-title">Reports</h2>

<div class="dashboard-modules">
    <a href="{{ route('reports.index') }}" class="dashboard-module">
        <div class="module-icon">📈</div>
        <h3>Reports Dashboard</h3>
        <p>View available business, inventory, and asset reports.</p>
        <span>Open Module →</span>
    </a>
</div>

<h2 class="dashboard-section-title">Recent Sales</h2>

<div class="dashboard-table-card">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Customer</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSales as $sale)
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                    <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ $sale->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="dashboard-empty">
                        No sales records yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 class="dashboard-section-title">Recent Customer Credits</h2>

<div class="dashboard-table-card">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Credit Number</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentCredits as $credit)
                <tr>
                    <td>{{ $credit->credit_number }}</td>
                    <td>{{ $credit->customer_name }}</td>
                    <td>₱{{ number_format($credit->total_amount, 2) }}</td>
                    <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                    <td>₱{{ number_format($credit->balance, 2) }}</td>
                    <td>{{ $credit->due_date ?? 'No due date' }}</td>
                    <td>{{ ucfirst($credit->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="dashboard-empty">
                        No customer credits recorded yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection


