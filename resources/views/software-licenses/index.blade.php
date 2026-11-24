
@extends('layouts.quickbuy')

@section('title', 'Software Licenses')
@section('header', 'Software License Management')

@section('content')
<div class="page-header">
    <div>
        <h1>Software License Management</h1>
        <p>Manage software licenses, expiration dates, and installations.</p>
    </div>
    <a href="{{ route('software-licenses.create') }}" class="btn btn-primary">
        + Add Software License
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Licenses</span>
        <h2>{{ $totalLicenses }}</h2>
    </div>
    <div class="stat-card">
        <span>Active Licenses</span>
        <h2>{{ $activeLicenses }}</h2>
    </div>
    <div class="stat-card">
        <span>Expired Licenses</span>
        <h2>{{ $expiredLicenses }}</h2>
    </div>
    <div class="stat-card">
        <span>Active Installations</span>
        <h2>{{ $totalInstallations }}</h2>
    </div>
</div>

<div class="card">
    <h3>Software License Records</h3>

    <form method="GET" action="{{ route('software-licenses.index') }}" class="search-form">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search software or vendor..."
        >

        <select name="status">
            <option value="">All Statuses</option>
            @foreach(['Active', 'Inactive', 'Expired'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ $status }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('software-licenses.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Software</th>
                    <th>Vendor</th>
                    <th>License Type</th>
                    <th>License Slots</th>
                    <th>Expiration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($licenses as $license)
                    @php
                        $isExpired = $license->expiration_date
                            && $license->expiration_date->lt(today());

                        $displayStatus = $isExpired ? 'Expired' : $license->status;

                        $statusClass = match($displayStatus) {
                            'Active' => 'status-success',
                            'Expired' => 'status-danger',
                            default => 'status-warning',
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $license->software_name }}</strong><br>
                            <small>{{ $license->license_key ?: 'No license key' }}</small>
                        </td>
                        <td>{{ $license->vendor ?: '-' }}</td>
                        <td>{{ $license->license_type }}</td>
                        <td>
                            {{ $license->installed_count }} /
                            {{ $license->number_of_licenses }}
                            <br>
                            <small>Installed / Total</small>
                        </td>
                        <td>{{ $license->expiration_date?->format('M d, Y') ?? 'No expiration' }}</td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('software-licenses.installations', $license) }}"
                               class="btn btn-primary">
                                Installations
                            </a>

                            <a href="{{ route('software-licenses.edit', $license) }}"
                               class="btn btn-secondary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No software licenses found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $licenses->links() }}
    </div>
</div>
@endsection
