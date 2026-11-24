
@extends('layouts.quickbuy')

@section('title', $pageTitle)

@section('content')
<style>
    .employee-form-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        max-width: 850px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }

    .employee-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .employee-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .employee-form-group.full {
        grid-column: 1 / -1;
    }

    .employee-form-group input,
    .employee-form-group textarea {
        padding: 11px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font: inherit;
        width: 100%;
    }

    .employee-form-group textarea {
        min-height: 90px;
        resize: vertical;
    }

    .employee-form-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .employee-form-btn {
        padding: 11px 17px;
        border: none;
        border-radius: 6px;
        background: #8b6246;
        color: white;
        text-decoration: none;
        cursor: pointer;
    }

    .employee-form-btn:hover {
        background: #4b2e20;
    }

    .employee-error {
        color: #a33b32;
        font-size: 13px;
    }

    @media(max-width: 650px) {
        .employee-form-grid {
            grid-template-columns: 1fr;
        }

        .employee-form-group.full {
            grid-column: auto;
        }
    }
</style>

<div class="employee-form-card">
    <h1>{{ $pageTitle }}</h1>
    <p style="color:#777;margin:8px 0 22px;">
        Enter the employee's information below.
    </p>

    @if($errors->any())
        <div style="background:#fce8e6;color:#a33b32;padding:12px;border-radius:6px;margin-bottom:18px;">
            Please correct the following errors:
            <ul style="margin:8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $employee->exists
              ? route('employees.update', $employee)
              : route('employees.store') }}">

        @csrf

        @if($employee->exists)
            @method('PUT')
        @endif

        <div class="employee-form-grid">
            <div class="employee-form-group">
                <label>Employee Code *</label>
                <input
                    type="text"
                    name="employee_code"
                    value="{{ old('employee_code', $employee->employee_code) }}"
                    placeholder="Example: EMP-001"
                    required
                >
                @error('employee_code')
                    <span class="employee-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label>Full Name *</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $employee->name) }}"
                    placeholder="Employee full name"
                    required
                >
                @error('name')
                    <span class="employee-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label>Position</label>
                <input
                    type="text"
                    name="position"
                    value="{{ old('position', $employee->position) }}"
                    placeholder="Example: Cashier"
                >
            </div>

            <div class="employee-form-group">
                <label>Phone Number</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $employee->phone) }}"
                    placeholder="Phone number"
                >
            </div>

            <div class="employee-form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $employee->email) }}"
                    placeholder="employee@email.com"
                >
                @error('email')
                    <span class="employee-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="employee-form-group full">
                <label>Address</label>
                <textarea name="address" placeholder="Employee address">{{ old('address', $employee->address) }}</textarea>
            </div>
        </div>

        <div class="employee-form-actions">
            <button type="submit" class="employee-form-btn">
                {{ $employee->exists ? 'Update Employee' : 'Save Employee' }}
            </button>

            <a href="{{ route('employees.index') }}"
               class="employee-form-btn"
               style="background:#777;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
