<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetDisposal;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssetDisposalController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetDisposal::with(['asset', 'recordedBy']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('disposal_method', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('asset_tag', 'like', "%{$search}%")
                            ->orWhere('asset_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disposals = $query->latest()->paginate(10)->withQueryString();

        $totalRecords = AssetDisposal::count();
        $pendingRecords = AssetDisposal::where('status', 'Pending')->count();
        $completedRecords = AssetDisposal::where('status', 'Completed')->count();
        $retiredAssets = Asset::where('status', 'Retired')->count();

        return view('asset-disposals.index', compact(
            'disposals',
            'totalRecords',
            'pendingRecords',
            'completedRecords',
            'retiredAssets'
        ));
    }

    public function create()
    {
        $assets = Asset::where('status', '!=', 'Retired')
            ->whereDoesntHave('assignments', function ($q) {
                $q->where('status', 'Assigned');
            })
            ->whereDoesntHave('maintenanceRecords', function ($q) {
                $q->whereIn('status', ['Pending', 'In Progress']);
            })
            ->orderBy('asset_name')
            ->get();

        return view('asset-disposals.form', [
            'disposal' => new AssetDisposal(),
            'assets' => $assets,
            'pageTitle' => 'Record Asset Disposal',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'disposal_method' => [
                'required',
                Rule::in(['Sale', 'Donation', 'Recycling', 'Scrap', 'Destruction', 'Other']),
            ],
            'reason' => ['required', 'string', 'max:3000'],
            'retirement_date' => ['required', 'date'],
            'disposal_date' => ['nullable', 'date', 'after_or_equal:retirement_date'],
            'disposal_value' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($validated) {
            $asset = Asset::whereKey($validated['asset_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($asset->status === 'Retired') {
                throw ValidationException::withMessages([
                    'asset_id' => 'This asset is already retired.',
                ]);
            }

            $hasActiveAssignment = AssetAssignment::where('asset_id', $asset->id)
                ->where('status', 'Assigned')
                ->exists();

            if ($hasActiveAssignment) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Return or transfer this asset before disposal.',
                ]);
            }

            $hasActiveMaintenance = MaintenanceRecord::where('asset_id', $asset->id)
                ->whereIn('status', ['Pending', 'In Progress'])
                ->exists();

            if ($hasActiveMaintenance) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Complete or cancel the active maintenance before disposal.',
                ]);
            }

            AssetDisposal::create([
                'asset_id' => $asset->id,
                'recorded_by' => Auth::id(),
                'disposal_method' => $validated['disposal_method'],
                'reason' => $validated['reason'],
                'retirement_date' => $validated['retirement_date'],
                'disposal_date' => $validated['disposal_date'] ?? null,
                'disposal_value' => $validated['disposal_value'] ?? 0,
                'status' => 'Pending',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Retired assets can no longer be assigned or installed.
            $asset->update(['status' => 'Retired']);
        });

        return redirect()->route('asset-disposals.index')
            ->with('success', 'Asset retirement and disposal record successfully saved.');
    }

    public function edit(AssetDisposal $assetDisposal)
    {
        $assetDisposal->load('asset');

        return view('asset-disposals.form', [
            'disposal' => $assetDisposal,
            'assets' => collect([$assetDisposal->asset])->filter(),
            'pageTitle' => 'Update Asset Disposal',
        ]);
    }

    public function update(Request $request, AssetDisposal $assetDisposal)
    {
        $validated = $request->validate([
            'disposal_method' => [
                'required',
                Rule::in(['Sale', 'Donation', 'Recycling', 'Scrap', 'Destruction', 'Other']),
            ],
            'reason' => ['required', 'string', 'max:3000'],
            'retirement_date' => ['required', 'date'],
            'disposal_date' => ['nullable', 'date', 'after_or_equal:retirement_date'],
            'disposal_value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Pending', 'Completed', 'Cancelled'])],
            'remarks' => ['nullable', 'string', 'max:3000'],
        ]);

        if (
            $validated['status'] === 'Completed'
            && empty($validated['disposal_date'])
        ) {
            return back()
                ->withErrors(['disposal_date' => 'Enter the actual disposal date before completing this record.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $assetDisposal) {
            $record = AssetDisposal::whereKey($assetDisposal->id)
                ->lockForUpdate()
                ->firstOrFail();

            $asset = Asset::whereKey($record->asset_id)
                ->lockForUpdate()
                ->firstOrFail();

            $record->update($validated);

            if ($validated['status'] === 'Cancelled' && $asset->status === 'Retired') {
                $asset->update(['status' => 'Available']);
            } elseif ($validated['status'] !== 'Cancelled' && $asset->status !== 'Retired') {
                $asset->update(['status' => 'Retired']);
            }
        });

        return redirect()->route('asset-disposals.index')
            ->with('success', 'Asset disposal record successfully updated.');
    }
}
