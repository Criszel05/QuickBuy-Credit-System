
@extends('layouts.quickbuy')

@section('title', 'Asset Disposal')
@section('header', 'Asset Disposal')

@section('content')
<div class="page-header">
    <div>
        <h1>Asset Disposal Management</h1>
        <p>Manage retired assets and their disposal records.</p>
    </div>
    <a href="{{ route('asset-disposals.create') }}" class="btn btn-primary">
        + Record Asset Disposal
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Records</span>
        <h2>{{ $totalRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>Pending Disposal</span>
        <h2>{{ $pendingRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>Completed Disposal</span>
        <h2>{{ $completedRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>Retired Assets</span>
        <h2>{{ $retiredAssets }}</h2>
    </div>
</div>

<div class="card">
    <h3>Asset Disposal History</h3>

    <form method="GET" action="{{ route('asset-disposals.index') }}" class="search-form">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search asset or disposal reason..."
        >

        <select name="status">
            <option value="">All Statuses</option>
            @foreach(['Pending', 'Completed', 'Cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ $status }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('asset-disposals.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>Method</th>
                    <th>Reason</th>
                    <th>Retirement Date</th>
                    <th>Disposal Date</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disposals as $disposal)
                    <tr>
                        <td>{{ $disposal->asset->asset_tag ?? '-' }}</td>
                        <td>{{ $disposal->asset->asset_name ?? 'Deleted Asset' }}</td>
                        <td>{{ $disposal->disposal_method }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($disposal->reason, 40) }}</td>
                        <td>{{ $disposal->retirement_date?->format('M d, Y') }}</td>
                        <td>{{ $disposal->disposal_date?->format('M d, Y') ?? '-' }}</td>
                        <td>₱{{ number_format((float) $disposal->disposal_value, 2) }}</td>
                        <td>
                            <span class="status-badge
                                {{ $disposal->status === 'Completed' ? 'status-success' :
                                    ($disposal->status === 'Cancelled' ? 'status-danger' : 'status-warning') }}">
                                {{ $disposal->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('asset-disposals.edit', $disposal) }}"
                               class="btn btn-secondary">
                                Update
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            No asset disposal records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $disposals->links() }}
    </div>
</div>
@endsection
