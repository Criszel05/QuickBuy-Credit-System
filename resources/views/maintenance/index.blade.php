
@extends('layouts.quickbuy')

@section('title', 'Maintenance Management')
@section('header', 'Maintenance Management')

@section('content')
<div class="page-header">
    <div>
        <h1>Maintenance Management</h1>
        <p>Monitor asset repairs, maintenance reports, and service records.</p>
    </div>
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
        + Report Maintenance
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Records</span>
        <h2>{{ $totalRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>Pending</span>
        <h2>{{ $pendingRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>In Progress</span>
        <h2>{{ $ongoingRecords }}</h2>
    </div>
    <div class="stat-card">
        <span>Completed</span>
        <h2>{{ $completedRecords }}</h2>
    </div>
</div>

<div class="card">
    <h3>Maintenance History</h3>

    <form method="GET" action="{{ route('maintenance.index') }}" class="search-form">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search maintenance, asset, or technician..."
        >

        <select name="status">
            <option value="">All Statuses</option>
            @foreach(['Pending', 'In Progress', 'Completed', 'Cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ $status }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Maintenance No.</th>
                    <th>Asset</th>
                    <th>Problem</th>
                    <th>Type</th>
                    <th>Technician</th>
                    <th>Reported Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record->maintenance_number }}</td>
                        <td>
                            <strong>{{ $record->asset->asset_name ?? 'Deleted Asset' }}</strong><br>
                            <small>{{ $record->asset->asset_tag ?? '-' }}</small>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($record->problem_description, 45) }}</td>
                        <td>{{ $record->maintenance_type }}</td>
                        <td>{{ $record->technician ?: 'Not assigned' }}</td>
                        <td>{{ $record->reported_date?->format('M d, Y') }}</td>
                        <td>
                            @php
                                $statusClass = match($record->status) {
                                    'Completed' => 'status-success',
                                    'In Progress' => 'status-warning',
                                    'Cancelled' => 'status-danger',
                                    default => 'status-warning',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $record->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('maintenance.edit', $record) }}"
                               class="btn btn-secondary">
                                Update
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            No maintenance records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $records->links() }}
    </div>
</div>
@endsection
