@extends('layouts.quickbuy')

@section('title', $pageTitle)

@section('content')
<div class="form-page">
    <div class="form-heading">
        <div>
            <h1>{{ $pageTitle }}</h1>
            <p>Enter the asset information below.</p>
        </div>

        <a href="{{ route('assets.index') }}" class="btn-back">
            Back to Asset Records
        </a>
    </div>

    @if ($errors->any())
        <div class="error-box">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $asset->exists ? route('assets.update', $asset) : route('assets.store') }}"
        class="asset-form"
    >
        @csrf

        @if($asset->exists)
            @method('PUT')
        @endif

        <h3>Basic Information</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Asset Tag *</label>
                <input
                    type="text"
                    name="asset_tag"
                    value="{{ old('asset_tag', $asset->asset_tag) }}"
                    placeholder="Example: QB-IT-001"
                    required
                >
            </div>

            <div class="form-group">
                <label>Asset Name *</label>
                <input
                    type="text"
                    name="asset_name"
                    value="{{ old('asset_name', $asset->asset_name) }}"
                    placeholder="Example: Desktop Computer"
                    required
                >
            </div>

            <div class="form-group">
                <label>Asset Type *</label>
                <input
                    type="text"
                    name="asset_type"
                    value="{{ old('asset_type', $asset->asset_type) }}"
                    placeholder="Example: Computer, Printer, Furniture"
                    required
                >
            </div>

            <div class="form-group">
                <label>Brand</label>
                <input
                    type="text"
                    name="brand"
                    value="{{ old('brand', $asset->brand) }}"
                    placeholder="Example: Dell"
                >
            </div>

            <div class="form-group">
                <label>Model</label>
                <input
                    type="text"
                    name="model"
                    value="{{ old('model', $asset->model) }}"
                    placeholder="Enter model"
                >
            </div>

            <div class="form-group">
                <label>Serial Number</label>
                <input
                    type="text"
                    name="serial_number"
                    value="{{ old('serial_number', $asset->serial_number) }}"
                    placeholder="Enter serial number"
                >
            </div>

            <div class="form-group">
                <label>Supplier</label>
                <input
                    type="text"
                    name="supplier"
                    value="{{ old('supplier', $asset->supplier) }}"
                    placeholder="Supplier name"
                >
            </div>

            <div class="form-group">
                <label>Location</label>
                <input
                    type="text"
                    name="location"
                    value="{{ old('location', $asset->location) }}"
                    placeholder="Example: Office Room 1"
                >
            </div>

            <div class="form-group">
                <label>Purchase Date</label>
                <input
                    type="date"
                    name="purchase_date"
                    value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}"
                >
            </div>

            <div class="form-group">
                <label>Purchase Cost (₱)</label>
                <input
                    type="number"
                    name="purchase_cost"
                    min="0"
                    step="0.01"
                    value="{{ old('purchase_cost', $asset->purchase_cost) }}"
                    placeholder="0.00"
                >
            </div>

            <div class="form-group">
                <label>Condition *</label>
                <select name="condition" required>
                    @foreach(['Good', 'Fair', 'Poor', 'For Repair'] as $condition)
                        <option
                            value="{{ $condition }}"
                            @selected(old('condition', $asset->condition ?: 'Good') === $condition)
                        >
                            {{ $condition }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    @foreach(['Available', 'Assigned', 'Under Maintenance', 'Retired'] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(old('status', $asset->status ?: 'Available') === $status)
                        >
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group description-group">
            <label>Description</label>
            <textarea
                name="description"
                rows="4"
                placeholder="Additional asset details..."
            >{{ old('description', $asset->description) }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('assets.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">
                {{ $asset->exists ? 'Update Asset' : 'Save Asset' }}
            </button>
        </div>
    </form>
</div>

<style>
    .form-page {
        max-width:1000px;
        margin:auto;
    }

    .form-heading {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        margin-bottom:22px;
    }

    .form-heading h1 {
        color:#4b2e20;
        margin:0 0 6px;
    }

    .form-heading p {
        color:#806d60;
        margin:0;
    }

    .asset-form {
        background:#fff;
        border:1px solid #eadfd2;
        border-radius:12px;
        padding:25px;
    }

    .asset-form h3 {
        color:#4b2e20;
        margin:0 0 20px;
        padding-bottom:12px;
        border-bottom:1px solid #eee5dc;
    }

    .form-grid {
        display:grid;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        gap:18px;
    }

    .form-group {
        display:flex;
        flex-direction:column;
        gap:7px;
    }

    .form-group label {
        color:#4b2e20;
        font-size:13px;
        font-weight:600;
    }

    .form-group input, .form-group select, .form-group textarea {
        width:100%;
        padding:11px 12px;
        border:1px solid #ddcdbd;
        border-radius:7px;
        font-family:Arial,sans-serif;
        color:#4b2e20;
        background:#fff;
    }

    .description-group {
        margin-top:18px;
    }

    .form-actions {
        display:flex;
        justify-content:flex-end;
        gap:10px;
        margin-top:25px;
        padding-top:18px;
        border-top:1px solid #eee5dc;
    }

    .btn-back, .btn-cancel, .btn-save {
        display:inline-block;
        padding:10px 15px;
        border:0;
        border-radius:7px;
        text-decoration:none;
        font-size:13px;
        font-weight:600;
        cursor:pointer;
    }

    .btn-back, .btn-cancel {
        background:#eee4d8;
        color:#4b2e20;
    }

    .btn-save {
        background:#4b2e20;
        color:#fff;
    }

    .error-box {
        background:#fbe9e7;
        color:#922b21;
        border:1px solid #efc2bd;
        border-radius:8px;
        padding:12px 18px;
        margin-bottom:18px;
    }

    @media(max-width:650px) {
        .form-grid {
            grid-template-columns:1fr;
        }

        .form-heading {
            align-items:flex-start;
            flex-direction:column;
        }
    }
</style>
@endsection