@extends('layouts.quickbuy')

@section('title', 'Asset Records')

@section('content')
<div class="page-header">
    <div>
        <h1>Asset Records</h1>
        <p>Manage and monitor company equipment and properties.</p>
    </div>

    <a href="{{ route('assets.create') }}" class="btn-primary">
        + Add New Asset
    </a>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <span>Total Assets</span>
        <h2>{{ $totalAssets }}</h2>
    </div>

    <div class="summary-card">
        <span>Available</span>
        <h2>{{ $availableAssets }}</h2>
    </div>

    <div class="summary-card">
        <span>Assigned</span>
        <h2>{{ $assignedAssets }}</h2>
    </div>

    <div class="summary-card">
        <span>Under Maintenance</span>
        <h2>{{ $maintenanceAssets }}</h2>
    </div>
</div>

<div class="content-card">
    <form method="GET" action="{{ route('assets.index') }}" class="filter-form">
        <input
            type="text"
            name="search"
            placeholder="Search asset tag, name, brand, serial number..."
            value="{{ request('search') }}"
        >

        <select name="status">
            <option value="">All Status</option>
            <option value="Available" @selected(request('status') === 'Available')>Available</option>
            <option value="Assigned" @selected(request('status') === 'Assigned')>Assigned</option>
            <option value="Under Maintenance" @selected(request('status') === 'Under Maintenance')>Under Maintenance</option>
            <option value="Retired" @selected(request('status') === 'Retired')>Retired</option>
        </select>

        <button type="submit" class="btn-primary">Search</button>
        <a href="{{ route('assets.index') }}" class="btn-secondary">Clear</a>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>Type</th>
                    <th>Brand / Model</th>
                    <th>Location</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Purchase Cost</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td><strong>{{ $asset->asset_tag }}</strong></td>
                        <td>{{ $asset->asset_name }}</td>
                        <td>{{ $asset->asset_type }}</td>
                        <td>
                            {{ $asset->brand ?: '—' }}
                            {{ $asset->model ? ' / '.$asset->model : '' }}
                        </td>
                        <td>{{ $asset->location ?: '—' }}</td>
                        <td>{{ $asset->condition }}</td>
                        <td>
                            <span class="status-pill">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td>
                            ₱{{ number_format((float) $asset->purchase_cost, 2) }}
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('assets.edit', $asset) }}" class="btn-small">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('assets.destroy', $asset) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this asset?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-small btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            No asset records found. Click "Add New Asset" to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-area">
        {{ $assets->links() }}
    </div>
</div>

<style>
    .page-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        margin-bottom:22px;
    }

    .page-header h1 {
        margin:0 0 6px;
        color:#4b2e20;
    }

    .page-header p {
        margin:0;
        color:#806d60;
    }

    .summary-grid {
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:15px;
        margin-bottom:22px;
    }

    .summary-card, .content-card {
        background:#fff;
        border:1px solid #eadfd2;
        border-radius:12px;
        padding:20px;
    }

    .summary-card span {
        color:#806d60;
        font-size:13px;
    }

    .summary-card h2 {
        margin:9px 0 0;
        color:#4b2e20;
        font-size:27px;
    }

    .filter-form {
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-bottom:20px;
    }

    .filter-form input {
        flex:1;
        min-width:220px;
    }

    .filter-form input, .filter-form select {
        padding:11px 12px;
        border:1px solid #ddcdbd;
        border-radius:7px;
        background:#fff;
        color:#4b2e20;
    }

    .btn-primary, .btn-secondary, .btn-small {
        display:inline-block;
        border:0;
        border-radius:7px;
        padding:10px 14px;
        text-decoration:none;
        cursor:pointer;
        font-size:13px;
        font-weight:600;
    }

    .btn-primary {
        background:#4b2e20;
        color:#fff;
    }

    .btn-secondary {
        background:#eee4d8;
        color:#4b2e20;
    }

    .table-wrapper {
        width:100%;
        overflow-x:auto;
    }

    .data-table {
        width:100%;
        border-collapse:collapse;
        min-width:950px;
    }

    .data-table th, .data-table td {
        padding:13px 11px;
        border-bottom:1px solid #eee5dc;
        text-align:left;
        font-size:13px;
    }

    .data-table th {
        background:#f7f1e8;
        color:#4b2e20;
    }

    .status-pill {
        display:inline-block;
        background:#eee4d8;
        color:#4b2e20;
        border-radius:20px;
        padding:5px 9px;
        font-size:12px;
    }

    .action-buttons {
        display:flex;
        align-items:center;
        gap:6px;
    }

    .action-buttons form {
        margin:0;
    }

    .btn-small {
        background:#eee4d8;
        color:#4b2e20;
        padding:7px 10px;
    }

    .btn-danger {
        background:#f8e2df;
        color:#9b3025;
    }

    .empty-state {
        text-align:center !important;
        color:#806d60;
        padding:30px !important;
    }

    .pagination-area {
        margin-top:18px;
    }

    @media(max-width:800px) {
        .summary-grid {
            grid-template-columns:repeat(2, minmax(0, 1fr));
        }

        .page-header {
            align-items:flex-start;
            flex-direction:column;
        }
    }
</style>
@endsection