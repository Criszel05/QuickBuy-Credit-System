<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetTransfer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetAssignment::with([
            'asset',
            'employee',
            'assignedBy',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('asset', function ($assetQuery) use ($search) {
                    $assetQuery->where('asset_tag', 'like', "%{$search}%")
                        ->orWhere('asset_name', 'like', "%{$search}%");
                })->orWhereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->latest()->paginate(10)->withQueryString();

        $activeAssignments = AssetAssignment::where('status', 'Assigned')->count();
        $returnedAssignments = AssetAssignment::where('status', 'Returned')->count();
        $availableAssets = Asset::where('status', 'Available')->count();

        return view('asset-assignments.index', compact(
            'assignments',
            'activeAssignments',
            'returnedAssignments',
            'availableAssets'
        ));
    }

    public function create()
    {
        $assets = Asset::where('status', 'Available')
            ->orderBy('asset_name')
            ->get();

        $employees = Employee::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('asset-assignments.create', compact(
            'assets',
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'assigned_date' => ['required', 'date'],
            'condition_on_release' => [
                'required',
                Rule::in(['Good', 'Fair', 'Poor', 'For Repair']),
            ],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated) {
            $asset = Asset::whereKey($validated['asset_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $employee = Employee::whereKey($validated['employee_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($asset->status !== 'Available') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'This asset is no longer available for assignment.',
                ]);
            }

            if (!$employee->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'employee_id' => 'This employee is inactive.',
                ]);
            }

            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'assigned_by' => Auth::id(),
                'assigned_date' => $validated['assigned_date'],
                'condition_on_release' => $validated['condition_on_release'],
                'status' => 'Assigned',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $asset->update([
                'status' => 'Assigned',
            ]);
        });

        return redirect()
            ->route('asset-assignments.index')
            ->with('success', 'Asset successfully assigned to employee.');
    }

    public function returnForm(AssetAssignment $assignment)
    {
        abort_unless($assignment->status === 'Assigned', 404);

        $assignment->load(['asset', 'employee']);

        return view('asset-assignments.return', compact('assignment'));
    }

    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $validated = $request->validate([
            'return_date' => ['required', 'date'],
            'condition_on_return' => [
                'required',
                Rule::in(['Good', 'Fair', 'Poor', 'For Repair']),
            ],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $assignment) {
            $lockedAssignment = AssetAssignment::whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAssignment->status !== 'Assigned') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'return_date' => 'This assignment has already been returned or transferred.',
                ]);
            }

            $asset = Asset::whereKey($lockedAssignment->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAssignment->update([
                'return_date' => $validated['return_date'],
                'condition_on_return' => $validated['condition_on_return'],
                'status' => 'Returned',
                'remarks' => $validated['remarks'] ?? $lockedAssignment->remarks,
            ]);

            $asset->update([
                'status' => $validated['condition_on_return'] === 'For Repair'
                    ? 'Under Maintenance'
                    : 'Available',
                'condition' => $validated['condition_on_return'],
            ]);
        });

        return redirect()
            ->route('asset-assignments.index')
            ->with('success', 'Asset return successfully recorded.');
    }

    public function transferForm(AssetAssignment $assignment)
    {
        abort_unless($assignment->status === 'Assigned', 404);

        $assignment->load(['asset', 'employee']);

        $employees = Employee::where('is_active', true)
            ->where('id', '!=', $assignment->employee_id)
            ->orderBy('name')
            ->get();

        return view('asset-assignments.transfer', compact(
            'assignment',
            'employees'
        ));
    }

    public function transfer(Request $request, AssetAssignment $assignment)
    {
        $validated = $request->validate([
            'to_employee_id' => ['required', 'integer', 'exists:employees,id'],
            'transfer_date' => ['required', 'date'],
            'to_location' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string', 'max:2000'],
            'condition_on_release' => [
                'required',
                Rule::in(['Good', 'Fair', 'Poor', 'For Repair']),
            ],
        ]);

        DB::transaction(function () use ($validated, $assignment) {
            $lockedAssignment = AssetAssignment::whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAssignment->status !== 'Assigned') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'to_employee_id' => 'This assignment is no longer active.',
                ]);
            }

            $asset = Asset::whereKey($lockedAssignment->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $newEmployee = Employee::whereKey($validated['to_employee_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!$newEmployee->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'to_employee_id' => 'The selected employee is inactive.',
                ]);
            }

            if ($newEmployee->id === $lockedAssignment->employee_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'to_employee_id' => 'Choose a different employee.',
                ]);
            }

            $fromEmployeeId = $lockedAssignment->employee_id;
            $fromLocation = $asset->location;

            AssetTransfer::create([
                'asset_id' => $asset->id,
                'from_employee_id' => $fromEmployeeId,
                'to_employee_id' => $newEmployee->id,
                'from_location' => $fromLocation,
                'to_location' => $validated['to_location'] ?? $fromLocation,
                'transferred_by' => Auth::id(),
                'transfer_date' => $validated['transfer_date'],
                'reason' => $validated['reason'],
            ]);

            // Close the previous employee's assignment.
            $lockedAssignment->update([
                'return_date' => $validated['transfer_date'],
                'condition_on_return' => $validated['condition_on_release'],
                'status' => 'Returned',
                'remarks' => trim(
                    ($lockedAssignment->remarks ?? '') .
                    ' | Transferred to ' . $newEmployee->name
                ),
            ]);

            // Create a new active assignment for the receiving employee.
            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $newEmployee->id,
                'assigned_by' => Auth::id(),
                'assigned_date' => $validated['transfer_date'],
                'condition_on_release' => $validated['condition_on_release'],
                'status' => 'Assigned',
                'remarks' => 'Received through asset transfer.',
            ]);

            $asset->update([
                'status' => 'Assigned',
                'location' => $validated['to_location'] ?? $fromLocation,
                'condition' => $validated['condition_on_release'],
            ]);
        });

        return redirect()
            ->route('asset-assignments.index')
            ->with('success', 'Asset successfully transferred to the new employee.');
    }
}
