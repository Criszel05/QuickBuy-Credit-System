
@extends('layouts.quickbuy')

@section('title', 'Return Asset')

@section('content')
<div style="background:white;padding:25px;border-radius:10px;max-width:750px;">
    <h1>Return Assigned Asset</h1>

    <div style="background:#f7f1e8;padding:15px;border-radius:8px;margin:18px 0;">
        <p><strong>Asset:</strong> {{ $assignment->asset->asset_tag }} - {{ $assignment->asset->asset_name }}</p>
        <p><strong>Employee:</strong> {{ $assignment->employee->name }}</p>
        <p><strong>Assigned Date:</strong> {{ $assignment->assigned_date?->format('M d, Y') }}</p>
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

    <form method="POST" action="{{ route('asset-assignments.return', $assignment) }}">
        @csrf
        @method('PATCH')

        <div style="margin:18px 0;">
            <label>Return Date *</label>
            <input type="date" name="return_date"
                   value="{{ old('return_date', now()->toDateString()) }}"
                   required style="display:block;width:100%;padding:11px;margin-top:7px;">
        </div>

        <div style="margin:18px 0;">
            <label>Condition Upon Return *</label>
            <select name="condition_on_return" required
                    style="display:block;width:100%;padding:11px;margin-top:7px;">
                @foreach(['Good', 'Fair', 'Poor', 'For Repair'] as $condition)
                    <option value="{{ $condition }}" @selected(old('condition_on_return', 'Good') === $condition)>
                        {{ $condition }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin:18px 0;">
            <label>Return Remarks</label>
            <textarea name="remarks" rows="3"
                      style="display:block;width:100%;padding:11px;margin-top:7px;">{{ old('remarks') }}</textarea>
        </div>

        <button type="submit"
                style="background:#39744b;color:white;border:0;padding:12px 18px;border-radius:6px;"
                onclick="return confirm('Confirm asset return?')">
            Confirm Return
        </button>

        <a href="{{ route('asset-assignments.index') }}" style="margin-left:10px;">
            Cancel
        </a>
    </form>
</div>
@endsection
