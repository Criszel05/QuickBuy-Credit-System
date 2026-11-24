
@extends('layouts.quickbuy')

@section('title', $pageTitle)
@section('header', $pageTitle)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $pageTitle }}</h1>
        <p>Record the retirement and disposal of an asset.</p>
    </div>
    <a href="{{ route('asset-disposals.index') }}" class="btn btn-secondary">
        Back to Disposal
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

    <form method="POST"
        action="{{ $disposal->exists
            ? route('asset-disposals.update', $disposal)
            : route('asset-disposals.store') }}">

        @csrf

        @if($disposal->exists)
            @method('PUT')
        @endif

        @if(!$disposal->exists)
            <div class="form-group">
                <label for="asset_id">Select Asset *</label>
                <select name="asset_id" id="asset_id" required>
                    <option value="">Choose an available asset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                            {{ $asset->asset_tag }} - {{ $asset->asset_name }}
                            ({{ $asset->status }})
                        </option>
                    @endforeach
                </select>

                @if($assets->isEmpty())
                    <small>No eligible assets are available. Return assigned assets and finish active maintenance first.</small>
                @endif
            </div>
        @else
            <div class="form-group">
                <label>Asset</label>
                <input
                    type="text"
                    value="{{ $disposal->asset->asset_tag ?? '-' }} - {{ $disposal->asset->asset_name ?? 'Deleted Asset' }}"
                    disabled
                >
            </div>
        @endif

        <div class="form-grid">
            <div class="form-group">
                <label for="disposal_method">Disposal Method *</label>
                <select name="disposal_method" id="disposal_method" required>
                    @foreach(['Sale', 'Donation', 'Recycling', 'Scrap', 'Destruction', 'Other'] as $method)
                        <option value="{{ $method }}"
                            @selected(old('disposal_method', $disposal->disposal_method) === $method)>
                            {{ $method }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="retirement_date">Retirement Date *</label>
                <input
                    type="date"
                    name="retirement_date"
                    id="retirement_date"
                    value="{{ old('retirement_date', $disposal->retirement_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="disposal_date">Disposal Date</label>
                <input
                    type="date"
                    name="disposal_date"
                    id="disposal_date"
                    value="{{ old('disposal_date', $disposal->disposal_date?->format('Y-m-d')) }}"
                >
            </div>

            <div class="form-group">
                <label for="disposal_value">Disposal Value (₱)</label>
                <input
                    type="number"
                    name="disposal_value"
                    id="disposal_value"
                    min="0"
                    step="0.01"
                    value="{{ old('disposal_value', $disposal->disposal_value ?? 0) }}"
                >
            </div>

            @if($disposal->exists)
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['Pending', 'Completed', 'Cancelled'] as $status)
                            <option value="{{ $status }}"
                                @selected(old('status', $disposal->status) === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="reason">Reason for Disposal *</label>
            <textarea name="reason" id="reason" rows="4" required
                placeholder="Explain why the asset is being retired...">{{ old('reason', $disposal->reason) }}</textarea>
        </div>

        <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3"
                placeholder="Additional notes...">{{ old('remarks', $disposal->remarks) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary"
            @disabled(!$disposal->exists && $assets->isEmpty())>
            {{ $disposal->exists ? 'Update Disposal Record' : 'Save Disposal Record' }}
        </button>

        <a href="{{ route('asset-disposals.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
@endsection
