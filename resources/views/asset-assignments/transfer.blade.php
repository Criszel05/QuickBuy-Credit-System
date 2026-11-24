
@extends('layouts.quickbuy')

@section('title', 'Transfer Asset')

@section('content')
<div style="background:white;padding:25px;border-radius:10px;max-width:750px;">
    <h1>Transfer Asset to Another Employee</h1>

    <div style="background:#f7f1e8;padding:15px;border-radius:8px;margin:18px 0;">
        <p><strong>Asset:</strong> {{ $assignment->asset->asset_tag }} - {{ $assignment->asset->asset_name }}</p>
        <p><strong>Current Employee:</strong> {{ $assignment->employee->name }}</p>
        <p><strong>Current Location:</strong> {{ $assignment->asset->location ?? 'Not specified' }}</p>
    </div>

    @if($errors->any())
        <div style="background:#fce8e6;color:#a33b32;padding:12px;border-radius:6px;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('asset-assignments.transfer', $assignment) }}">
        @csrf

        <div style="margin:18px 0;">
            <label>Transfer To Employee *</label>
            <select name="to_employee_id" required
                    style="display:block;width:100%;padding:11px;margin-top:7px;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('to_employee_id') == $employee->id)>
                        {{ $employee->employee_code }} - {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Transfer Date *</label>
            <input type="date" name="transfer_date"
                   value="{{ old('transfer_date', now()->toDateString()) }}"
                   required style="display:block;width:100%;padding:11px;margin-top:7px;">
        </div>

        <div style="margin:18px 0;">
            <label>New Location</label>
            <input type="text" name="to_location"
                   value="{{ old('to_location', $assignment->asset->location) }}"
                   placeholder="Enter new location"
                   style="display:block;width:100%;padding:11px;margin-top:7px;">
        </div>

        <div style="margin:18px 0;">
            <label>Asset Condition *</label>
            <select name="condition_on_release" required
                    style="display:block;width:100%;padding:11px;margin-top:7px;">
                @foreach(['Good', 'Fair', 'Poor', 'For Repair'] as $condition)
                    <option value="{{ $condition }}" @selected(old('condition_on_release', $assignment->asset->condition) === $condition)>
                        {{ $condition }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Reason for Transfer *</label>
            <textarea name="reason" rows="3" required
                      style="display:block;width:100%;padding:11px;margin-top:7px;">{{ old('reason') }}</textarea>
        </div>

        <button type="submit"
                style="background:#356b91;color:white;border:0;padding:12px 18px;border-radius:6px;"
                onclick="return confirm('Confirm transfer to the selected employee?')">
            Confirm Transfer
        </button>

        <a href="{{ route('asset-assignments.index') }}" style="margin-left:10px;">
            Cancel
        </a>
    </form>
</div>
@endsection
