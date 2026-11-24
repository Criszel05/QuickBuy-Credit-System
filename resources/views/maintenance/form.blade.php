
@extends('layouts.quickbuy')

@section('title', $pageTitle)
@section('header', $pageTitle)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $pageTitle }}</h1>
        <p>Record and manage asset maintenance information.</p>
    </div>
    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
        Back to Maintenance
    </a>
</div>

<div class="card">
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $record->exists
            ? route('maintenance.update', $record)
            : route('maintenance.store') }}"
    >
        @csrf

        @if($record->exists)
            @method('PUT')
        @endif

        @if(!$record->exists)
            <div class="form-group">
                <label for="asset_id">Asset *</label>
                <select name="asset_id" id="asset_id" required>
                    <option value="">Select Asset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}"
                            @selected(old('asset_id') == $asset->id)>
                            {{ $asset->asset_tag }} - {{ $asset->asset_name }}
                            ({{ $asset->status }})
                        </option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="form-group">
                <label>Asset</label>
                <input
                    type="text"
                    value="{{ $record->asset->asset_tag }} - {{ $record->asset->asset_name }}"
                    disabled
                >
            </div>
        @endif

        <div class="form-group">
            <label for="problem_description">Problem Description *</label>
            <textarea
                name="problem_description"
                id="problem_description"
                rows="4"
                required
            >{{ old('problem_description', $record->problem_description) }}</textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type *</label>
                <select name="maintenance_type" id="maintenance_type" required>
                    @foreach(['Preventive', 'Corrective', 'Emergency'] as $type)
                        <option value="{{ $type }}"
                            @selected(old('maintenance_type', $record->maintenance_type) === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="technician">Technician</label>
                <input
                    type="text"
                    name="technician"
                    id="technician"
                    value="{{ old('technician', $record->technician) }}"
                    placeholder="Enter technician name"
                >
            </div>

            <div class="form-group">
                <label for="reported_date">Reported Date *</label>
                <input
                    type="date"
                    name="reported_date"
                    id="reported_date"
                    value="{{ old('reported_date', $record->reported_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                    required
                >
            </div>

            @if($record->exists)
                <div class="form-group">
                    <label for="status">Maintenance Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['Pending', 'In Progress', 'Completed', 'Cancelled'] as $status)
                            <option value="{{ $status }}"
                                @selected(old('status', $record->status) === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="completed_date">Completed Date</label>
                    <input
                        type="date"
                        name="completed_date"
                        id="completed_date"
                        value="{{ old('completed_date', $record->completed_date?->format('Y-m-d')) }}"
                    >
                </div>

                <div class="form-group">
                    <label for="repair_cost">Repair Cost</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="repair_cost"
                        id="repair_cost"
                        value="{{ old('repair_cost', $record->repair_cost) }}"
                        placeholder="0.00"
                    >
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="repair_notes">Repair Notes</label>
            <textarea
                name="repair_notes"
                id="repair_notes"
                rows="4"
                placeholder="Enter repair details or remarks..."
            >{{ old('repair_notes', $record->repair_notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $record->exists ? 'Update Maintenance' : 'Save Maintenance Report' }}
        </button>

        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
@endsection
