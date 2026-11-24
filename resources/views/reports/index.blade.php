
@extends('layouts.quickbuy')

@section('title', 'Reports Dashboard')
@section('header', 'Reports Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Reports Dashboard</h1>
        <p>Overview of QuickBuy business operations and asset management.</p>
    </div>
</div>

<div class="card">
    <h3>Sales Date Filter</h3>

    <form method="GET" action="{{ route('reports.index') }}" class="search-form">
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" id="start_date"
                value="{{ $startDate }}">
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date"
                value="{{ $endDate }}">
        </div>

        <button type="submit" class="btn btn-primary">Apply Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<h2 class="section-title">Business Summary</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Sales</span>
        <h2>₱{{ number_format($totalSales, 2) }}</h2>
    </div>

    <div class="stat-card">
        <span>Sales Transactions</span>
        <h2>{{ number_format($salesCount) }}</h2>
    </div>

    <div class="stat-card">
        <span>Outstanding Credits</span>
        <h2>₱{{ number_format($outstandingCredits, 2) }}</h2>
    </div>

    <div class="stat-card">
        <span>Active Customers</span>
        <h2>{{ number_format($totalCustomers) }}</h2>
    </div>
</div>

<h2 class="section-title">Inventory Reports</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Active Products</span>
        <h2>{{ number_format($totalProducts) }}</h2>
    </div>

    <div class="stat-card">
        <span>Total Stock Quantity</span>
        <h2>{{ number_format($totalStock) }}</h2>
    </div>

    <div class="stat-card">
        <span>Low Stock Products</span>
        <h2>{{ number_format($lowStock) }}</h2>
    </div>
</div>

<h2 class="section-title">Employee and Asset Reports</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Active Employees</span>
        <h2>{{ number_format($totalEmployees) }}</h2>
    </div>

    <div class="stat-card">
        <span>Total Assets</span>
        <h2>{{ number_format($totalAssets) }}</h2>
    </div>

    <div class="stat-card">
        <span>Available Assets</span>
        <h2>{{ number_format($availableAssets) }}</h2>
    </div>

    <div class="stat-card">
        <span>Assigned Assets</span>
        <h2>{{ number_format($assignedAssets) }}</h2>
    </div>

    <div class="stat-card">
        <span>Under Maintenance</span>
        <h2>{{ number_format($maintenanceAssets) }}</h2>
    </div>

    <div class="stat-card">
        <span>Retired Assets</span>
        <h2>{{ number_format($retiredAssets) }}</h2>
    </div>
</div>

<div class="card">
    <h3>Asset Status Summary</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Asset Status</th>
                    <th>Total Assets</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assetStatusSummary as $item)
                    <tr>
                        <td>{{ $item->status }}</td>
                        <td>{{ number_format($item->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No asset records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h2 class="section-title">Maintenance Reports</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Pending Maintenance</span>
        <h2>{{ number_format($maintenancePending) }}</h2>
    </div>

    <div class="stat-card">
        <span>In Progress</span>
        <h2>{{ number_format($maintenanceInProgress) }}</h2>
    </div>

    <div class="stat-card">
        <span>Completed Maintenance</span>
        <h2>{{ number_format($maintenanceCompleted) }}</h2>
    </div>
</div>

<h2 class="section-title">Software License Reports</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Software Licenses</span>
        <h2>{{ number_format($totalLicenses) }}</h2>
    </div>

    <div class="stat-card">
        <span>Expired Licenses</span>
        <h2>{{ number_format($expiredLicenses) }}</h2>
    </div>

    <div class="stat-card">
        <span>Expiring Within 30 Days</span>
        <h2>{{ number_format($expiringLicenses) }}</h2>
    </div>
</div>

<h2 class="section-title">Asset Disposal Reports</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Disposal Records</span>
        <h2>{{ number_format($totalDisposals) }}</h2>
    </div>

    <div class="stat-card">
        <span>Pending Disposal</span>
        <h2>{{ number_format($pendingDisposals) }}</h2>
    </div>

    <div class="stat-card">
        <span>Completed Disposal</span>
        <h2>{{ number_format($completedDisposals) }}</h2>
    </div>

    <div class="stat-card">
        <span>Total Disposal Value</span>
        <h2>₱{{ number_format($disposalValue, 2) }}</h2>
    </div>
</div>

<h2 class="section-title">Recent Sales</h2>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Payment Method</th>
                    <th>Total</th>
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
                        <td colspan="5" class="empty-state">No sales records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h2 class="section-title">Recent Asset Disposals</h2>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Disposal Value</th>
                    <th>Retirement Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDisposals as $disposal)
                    <tr>
                        <td>{{ $disposal->asset_tag }}</td>
                        <td>{{ $disposal->asset_name }}</td>
                        <td>{{ $disposal->disposal_method }}</td>
                        <td>{{ $disposal->status }}</td>
                        <td>₱{{ number_format($disposal->disposal_value, 2) }}</td>
                        <td>{{ $disposal->retirement_date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No disposal records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
