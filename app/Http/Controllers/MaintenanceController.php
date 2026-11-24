<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['asset', 'reportedBy']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('maintenance_number', 'like', "%{$search}%")
                    ->orWhere('problem_description', 'like', "%{$search}%")
                    ->orWhere('technician', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('asset_tag', 'like', "%{$search}%")
                            ->orWhere('asset_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest()->paginate(10)->withQueryString();

        $totalRecords = MaintenanceRecord::count();
        $pendingRecords = MaintenanceRecord::where('status', 'Pending')->count();
        $ongoingRecords = MaintenanceRecord::where('status', 'In Progress')->count();
        $completedRecords = MaintenanceRecord::where('status', 'Completed')->count();

        return view('maintenance.index', compact(
            'records',
            'totalRecords',
            'pendingRecords',
            'ongoingRecords',
            'completedRecords'
        ));
    }

    public function create()
    {
        $assets = Asset::whereIn('status', ['Available', 'Under Maintenance'])
            ->orderBy('asset_name')
            ->get();

        return view('maintenance.form', [
            'record' => new MaintenanceRecord(),
            'assets' => $assets,
            'pageTitle' => 'Report Maintenance',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'problem_description' => ['required', 'string', 'max:3000'],
            'maintenance_type' => [
                'required',
                Rule::in(['Preventive', 'Corrective', 'Emergency']),
            ],
            'technician' => ['nullable', 'string', 'max:255'],
            'reported_date' => ['required', 'date'],
            'repair_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($validated) {
            $asset = Asset::whereKey($validated['asset_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($asset->status, ['Available', 'Under Maintenance'], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'Only available or currently maintained assets can be reported.',
                ]);
            }

            $hasActiveMaintenance = MaintenanceRecord::where('asset_id', $asset->id)
                ->whereIn('status', ['Pending', 'In Progress'])
                ->exists();

            if ($hasActiveMaintenance) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'This asset already has an active maintenance record.',
                ]);
            }

            MaintenanceRecord::create([
                'maintenance_number' => 'MNT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'asset_id' => $asset->id,
                'reported_by' => Auth::id(),
                'problem_description' => $validated['problem_description'],
                'maintenance_type' => $validated['maintenance_type'],
                'status' => 'Pending',
                'technician' => $validated['technician'] ?? null,
                'reported_date' => $validated['reported_date'],
                'repair_notes' => $validated['repair_notes'] ?? null,
            ]);

            $asset->update(['status' => 'Under Maintenance']);
        });

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance report successfully created.');
    }

    public function edit(MaintenanceRecord $maintenance)
    {
        $maintenance->load('asset');

        $assets = Asset::whereKey($maintenance->asset_id)->get();

        return view('maintenance.form', [
            'record' => $maintenance,
            'assets' => $assets,
            'pageTitle' => 'Update Maintenance Record',
        ]);
    }

    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        $validated = $request->validate([
            'problem_description' => ['required', 'string', 'max:3000'],
            'maintenance_type' => [
                'required',
                Rule::in(['Preventive', 'Corrective', 'Emergency']),
            ],
            'status' => [
                'required',
                Rule::in(['Pending', 'In Progress', 'Completed', 'Cancelled']),
            ],
            'technician' => ['nullable', 'string', 'max:255'],
            'reported_date' => ['required', 'date'],
            'completed_date' => ['nullable', 'date', 'after_or_equal:reported_date'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
            'repair_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if (
            $validated['status'] === 'Completed'
            && empty($validated['completed_date'])
        ) {
            return back()
                ->withErrors(['completed_date' => 'Please enter the completion date.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $maintenance) {
            $record = MaintenanceRecord::whereKey($maintenance->id)
                ->lockForUpdate()
                ->firstOrFail();

            $asset = Asset::whereKey($record->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $record->update($validated);

            if (in_array($validated['status'], ['Pending', 'In Progress'], true)) {
                if ($asset->status !== 'Retired') {
                    $asset->update(['status' => 'Under Maintenance']);
                }
            } elseif (
                in_array($validated['status'], ['Completed', 'Cancelled'], true)
                && $asset->status === 'Under Maintenance'
            ) {
                $asset->update(['status' => 'Available']);
            }
        });

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance record successfully updated.');
    }
}
