
@extends('layouts.quickbuy')

@section('title', 'Assign Asset')

@section('content')
<div style="background:white;padding:25px;border-radius:10px;max-width:800px;">
    <h1>Assign Asset to Employee</h1>
    <p style="color:#777;">Select an available asset and an active employee.</p>

    @if($errors->any())
        <div style="background:#fce8e6;color:#a33b32;padding:12px;border-radius:6px;margin:15px 0;">
            <ul style="margin:0 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($assets->isEmpty())
        <p style="background:#fff4df;padding:12px;border-radius:6px;">
            No available assets. Add an asset or return an assigned asset first.
        </p>
    @endif

    @if($employees->isEmpty())
        <p style="background:#fff4df;padding:12px;border-radius:6px;">
            No active employees found. Add or activate an employee first.
        </p>
    @endif

    <form method="POST" action="{{ route('asset-assignments.store') }}">
        @csrf

        <div style="margin:18px 0;">
            <label>Available Asset *</label>
            <select name="asset_id" required style="display:block;width:100%;padding:11px;margin-top:7px;">
                <option value="">Select Asset</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                        {{ $asset->asset_tag }} - {{ $asset->asset_name }}
                        ({{ $asset->location ?? 'No location' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Employee *</label>
            <select name="employee_id" required style="display:block;width:100%;padding:11px;margin-top:7px;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                        {{ $employee->employee_code }} - {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Assignment Date *</label>
            <input type="date" name="assigned_date"
                   value="{{ old('assigned_date', now()->toDateString()) }}"
                   required style="display:block;width:100%;padding:11px;margin-top:7px;">
        </div>

        <div style="margin:18px 0;">
            <label>Condition Upon Release *</label>
            <select name="condition_on_release" required style="display:block;width:100%;padding:11px;margin-top:7px;">
                @foreach(['Good', 'Fair', 'Poor', 'For Repair'] as $condition)
                    <option value="{{ $condition }}" @selected(old('condition_on_release', 'Good') === $condition)>
                        {{ $condition }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Remarks</label>
            <textarea name="remarks" rows="3"
                      style="display:block;width:100%;padding:11px;margin-top:7px;">{{ old('remarks') }}</textarea>
        </div>

        <button type="submit"
                {{ $assets->isEmpty() || $employees->isEmpty() ? 'disabled' : '' }}
                style="background:#8b6246;color:white;border:0;padding:12px 18px;border-radius:6px;cursor:pointer;">
            Save Assignment
        </button>

        <a href="{{ route('asset-assignments.index') }}"
           style="margin-left:8px;color:#4b2e20;">Cancel</a>
    </form>
</div>
@endsection
