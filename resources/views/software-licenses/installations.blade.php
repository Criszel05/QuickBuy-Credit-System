
@extends('layouts.quickbuy')

@section('title', 'Software Installations')
@section('header', 'Software Installations')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $softwareLicense->software_name }}</h1>
        <p>Manage installations of this software on registered assets.</p>
    </div>
    <a href="{{ route('software-licenses.index') }}" class="btn btn-secondary">
        Back to Licenses
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total License Slots</span>
        <h2>{{ $softwareLicense->number_of_licenses }}</h2>
    </div>
    <div class="stat-card">
        <span>Currently Installed</span>
        <h2>{{ $installedCount }}</h2>
    </div>
    <div class="stat-card">
        <span>Available Slots</span>
        <h2>{{ $availableCount }}</h2>
    </div>
</div>

<div class="card">
    <h3>Install Software on Asset</h3>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($availableCount > 0 && $softwareLicense->status === 'Active')
        <form
            method="POST"
            action="{{ route('software-licenses.install', $softwareLicense) }}"
        >
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="asset_id">Select Asset *</label>
                    <select name="asset_id" id="asset_id" required>
                        <option value="">Choose Asset</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                {{ $asset->asset_tag }} - {{ $asset->asset_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="installation_date">Installation Date *</label>
                    <input
                        type="date"
                        name="installation_date"
                        id="installation_date"
                        value="{{ old('installation_date', now()->format('Y-m-d')) }}"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" rows="2">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Install Software
            </button>
        </form>
    @else
        <div class="alert alert-error">
            No installation slots are available, or this license is not active.
        </div>
    @endif
</div>

<div class="card">
    <h3>Installation History</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Installation Date</th>
                    <th>Uninstallation Date</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($softwareLicense->installations->sortByDesc('created_at') as $installation)
                    <tr>
                        <td>
                            <strong>{{ $installation->asset->asset_name ?? 'Deleted Asset' }}</strong><br>
                            <small>{{ $installation->asset->asset_tag ?? '-' }}</small>
                        </td>
                        <td>{{ $installation->installation_date?->format('M d, Y') }}</td>
                        <td>{{ $installation->uninstallation_date?->format('M d, Y') ?? '-' }}</td>
                        <td>
                            <span class="status-badge {{ $installation->status === 'Installed' ? 'status-success' : 'status-warning' }}">
                                {{ $installation->status }}
                            </span>
                        </td>
                        <td>{{ $installation->notes ?: '-' }}</td>
                        <td>
                            @if($installation->status === 'Installed')
                                <form
                                    method="POST"
                                    action="{{ route('software-licenses.uninstall', [$softwareLicense, $installation]) }}"
                                    onsubmit="return confirm('Are you sure you want to uninstall this software?')"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger">
                                        Uninstall
                                    </button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            No installation history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
