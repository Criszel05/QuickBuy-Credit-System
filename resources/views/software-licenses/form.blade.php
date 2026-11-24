
@extends('layouts.quickbuy')

@section('title', $pageTitle)
@section('header', $pageTitle)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $pageTitle }}</h1>
        <p>Enter the software license information below.</p>
    </div>
    <a href="{{ route('software-licenses.index') }}" class="btn btn-secondary">
        Back to Licenses
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
        action="{{ $license->exists
            ? route('software-licenses.update', $license)
            : route('software-licenses.store') }}"
    >
        @csrf

        @if($license->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-group">
                <label for="software_name">Software Name *</label>
                <input
                    type="text"
                    name="software_name"
                    id="software_name"
                    value="{{ old('software_name', $license->software_name) }}"
                    placeholder="e.g. Microsoft Office"
                    required
                >
            </div>

            <div class="form-group">
                <label for="vendor">Vendor</label>
                <input
                    type="text"
                    name="vendor"
                    id="vendor"
                    value="{{ old('vendor', $license->vendor) }}"
                    placeholder="e.g. Microsoft"
                >
            </div>

            <div class="form-group">
                <label for="license_type">License Type *</label>
                <select name="license_type" id="license_type" required>
                    @foreach(['Per User', 'Per Device', 'Volume', 'Subscription', 'Perpetual', 'Other'] as $type)
                        <option value="{{ $type }}"
                            @selected(old('license_type', $license->license_type) === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="number_of_licenses">Number of Licenses *</label>
                <input
                    type="number"
                    name="number_of_licenses"
                    id="number_of_licenses"
                    min="1"
                    value="{{ old('number_of_licenses', $license->number_of_licenses ?? 1) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="purchase_cost">Purchase Cost</label>
                <input
                    type="number"
                    name="purchase_cost"
                    id="purchase_cost"
                    min="0"
                    step="0.01"
                    value="{{ old('purchase_cost', $license->purchase_cost) }}"
                    placeholder="0.00"
                >
            </div>

            <div class="form-group">
                <label for="purchase_date">Purchase Date</label>
                <input
                    type="date"
                    name="purchase_date"
                    id="purchase_date"
                    value="{{ old('purchase_date', $license->purchase_date?->format('Y-m-d')) }}"
                >
            </div>

            <div class="form-group">
                <label for="expiration_date">Expiration Date</label>
                <input
                    type="date"
                    name="expiration_date"
                    id="expiration_date"
                    value="{{ old('expiration_date', $license->expiration_date?->format('Y-m-d')) }}"
                >
            </div>

            <div class="form-group">
                <label for="license_key">License Key</label>
                <input
                    type="text"
                    name="license_key"
                    id="license_key"
                    value="{{ old('license_key', $license->license_key) }}"
                    placeholder="Enter license key"
                >
            </div>

            @if($license->exists)
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['Active', 'Inactive', 'Expired'] as $status)
                            <option value="{{ $status }}"
                                @selected(old('status', $license->status) === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea
                name="notes"
                id="notes"
                rows="4"
                placeholder="Additional information..."
            >{{ old('notes', $license->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $license->exists ? 'Update License' : 'Save License' }}
        </button>

        <a href="{{ route('software-licenses.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
@endsection
