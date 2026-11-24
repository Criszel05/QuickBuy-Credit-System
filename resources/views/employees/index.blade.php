
@extends('layouts.quickbuy')

@section('title', 'Employees')

@section('content')
<style>
    .employee-card {
        background: white;
        padding: 22px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }

    .employee-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .employee-stat {
        background: white;
        border-left: 5px solid #8b6246;
        padding: 18px;
        border-radius: 9px;
    }

    .employee-stat p {
        color: #777;
        margin-bottom: 8px;
    }

    .employee-stat h2 {
        color: #4b2e20;
    }

    .employee-btn {
        display: inline-block;
        padding: 9px 13px;
        background: #8b6246;
        color: white;
        text-decoration: none;
        border: 0;
        border-radius: 5px;
        cursor: pointer;
    }

    .employee-btn:hover {
        background: #4b2e20;
    }

    .employee-btn-danger {
        background: #a33b32;
    }

    .employee-btn-success {
        background: #39744b;
    }

    .employee-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .employee-table th,
    .employee-table td {
        padding: 12px;
        border-bottom: 1px solid #eee4d9;
        text-align: left;
    }

    .employee-table th {
        background: #f7f1e8;
    }

    .employee-table td form {
        display: inline-block;
    }

    .employee-status {
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 12px;
    }

    .employee-active {
        background: #e0f1e3;
        color: #28613a;
    }

    .employee-inactive {
        background: #f5dddd;
        color: #8c2929;
    }

    .employee-search {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 260px;
        max-width: 100%;
    }

    .employee-table-wrap {
        overflow-x: auto;
    }

    @media(max-width: 700px) {
        .employee-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="employee-card">
    <h1>Employee Management</h1>
    <p>Manage employee information and employment status.</p>
</div>

@if(session('success'))
    <div class="employee-card" style="color:green;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="employee-card" style="color:#a33b32;">
        {{ session('error') }}
    </div>
@endif

<div class="employee-stats">
    <div class="employee-stat">
        <p>Total Employees</p>
        <h2>{{ number_format($totalEmployees) }}</h2>
    </div>

    <div class="employee-stat">
        <p>Active Employees</p>
        <h2>{{ number_format($activeEmployees) }}</h2>
    </div>

    <div class="employee-stat">
        <p>Inactive Employees</p>
        <h2>{{ number_format($inactiveEmployees) }}</h2>
    </div>
</div>

<div class="employee-card">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:18px;">
        <h2>Employee Records</h2>

        <a href="{{ route('employees.create') }}" class="employee-btn">
            + Add Employee
        </a>
    </div>

    <form method="GET" action="{{ route('employees.index') }}"
          style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">

        <input
            type="text"
            name="search"
            class="employee-search"
            placeholder="Search employee..."
            value="{{ request('search') }}"
        >

        <select name="status" class="employee-search">
            <option value="">All Employees</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                Active
            </option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>

        <button type="submit" class="employee-btn">Search</button>

        <a href="{{ route('employees.index') }}" class="employee-btn">
            Reset
        </a>
    </form>

    <div class="employee-table-wrap">
        <table class="employee-table">
            <thead>
                <tr>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->position ?? '—' }}</td>
                        <td>{{ $employee->phone ?? '—' }}</td>
                        <td>{{ $employee->email ?? '—' }}</td>
                        <td>
                            @if($employee->is_active)
                                <span class="employee-status employee-active">Active</span>
                            @else
                                <span class="employee-status employee-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('employees.edit', $employee) }}"
                               class="employee-btn">
                                Edit
                            </a>

                            @if($employee->is_active)
                                <form method="POST"
                                      action="{{ route('employees.destroy', $employee) }}"
                                      onsubmit="return confirm('Deactivate this employee?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="employee-btn employee-btn-danger">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST"
                                      action="{{ route('employees.activate', $employee) }}"
                                      onsubmit="return confirm('Activate this employee?');">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="employee-btn employee-btn-success">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:25px;">
                            No employee records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $employees->links() }}
    </div>
</div>
@endsection
