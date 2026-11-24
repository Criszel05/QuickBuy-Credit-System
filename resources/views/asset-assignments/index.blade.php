
@extends('layouts.quickbuy')

@section('title', 'Asset Assignments')

@section('content')
<style>
.assignment-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 18px;
}
.assignment-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 18px;
}
.assignment-stat {
    background: white;
    padding: 18px;
    border-left: 5px solid #8b6246;
    border-radius: 8px;
}
.assignment-stat p { color: #777; margin: 0 0 8px; }
.assignment-stat h2 { color: #4b2e20; margin: 0; }
.assignment-btn {
    display: inline-block;
    padding: 8px 12px;
    background: #8b6246;
    color: white;
    border: 0;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    margin: 2px;
}
.assignment-btn:hover { background: #4b2e20; }
.assignment-btn.green { background: #39744b; }
.assignment-btn.blue { background: #356b91; }
.assignment-table-wrap { overflow-x: auto; }
.assignment-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}
.assignment-table th, .assignment-table td {
    padding: 12px;
    border-bottom: 1px solid #eee4d9;
    text-align: left;
}
.assignment-table th { background: #f7f1e8; }
.assignment-status {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 12px;
    background: #e0f1e3;
    color: #28613a;
}
.assignment-status.returned {
    background: #eee;
    color: #555;
}
@media(max-width:700px) {
    .assignment-stats { grid-template-columns: 1fr; }
}
</style>

<div class="assignment-card">
    <h1>Asset Assignment and Transfer</h1>
    <p>Manage asset assignments, returns, and employee transfers.</p>
</div>

@if(session('success'))
    <div class="assignment-card" style="color:green;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="assignment-card" style="color:#a33b32;">
        {{ session('error') }}
    </div>
@endif

<div class="assignment-stats">
    <div class="assignment-stat">
        <p>Currently Assigned</p>
        <h2>{{ $activeAssignments }}</h2>
    </div>
    <div class="assignment-stat">
        <p>Returned / Previous Assignments</p>
        <h2>{{ $returnedAssignments }}</h2>
    </div>
    <div class="assignment-stat">
        <p>Available Assets</p>
        <h2>{{ $availableAssets }}</h2>
    </div>
</div>

<div class="assignment-card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
        <h2>Assignment History</h2>
        <a href="{{ route('asset-assignments.create') }}" class="assignment-btn">
            + Assign Asset
        </a>
    </div>

    <form method="GET" action="{{ route('asset-assignments.index') }}"
          style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;">
        <input type="text" name="search"
               value="{{ request('search') }}"
               placeholder="Search asset or employee..."
               style="padding:10px;border:1px solid #ddd;border-radius:5px;">

        <select name="status"
                style="padding:10px;border:1px solid #ddd;border-radius:5px;">
            <option value="">All Statuses</option>
            <option value="Assigned" @selected(request('status') === 'Assigned')>Assigned</option>
            <option value="Returned" @selected(request('status') === 'Returned')>Returned</option>
        </select>

        <button class="assignment-btn" type="submit">Search</button>
        <a class="assignment-btn" href="{{ route('asset-assignments.index') }}">Reset</a>
    </form>

    <div class="assignment-table-wrap">
        <table class="assignment-table">
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset</th>
                    <th>Employee</th>
                    <th>Assigned Date</th>
                    <th>Return Date</th>
                    <th>Condition Released</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->asset->asset_tag ?? 'Asset deleted' }}</td>
                        <td>{{ $assignment->asset->asset_name ?? '—' }}</td>
                        <td>{{ $assignment->employee->name ?? '—' }}</td>
                        <td>{{ $assignment->assigned_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $assignment->return_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $assignment->condition_on_release ?? '—' }}</td>
                        <td>
                            <span class="assignment-status {{ $assignment->status === 'Returned' ? 'returned' : '' }}">
                                {{ $assignment->status }}
                            </span>
                        </td>
                        <td>
                            @if($assignment->status === 'Assigned')
                                <a class="assignment-btn green"
                                   href="{{ route('asset-assignments.return-form', $assignment) }}">
                                    Return
                                </a>
                                <a class="assignment-btn blue"
                                   href="{{ route('asset-assignments.transfer-form', $assignment) }}">
                                    Transfer
                                </a>
                            @else
                                <span style="color:#888;">Completed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:25px;">
                            No asset assignments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
