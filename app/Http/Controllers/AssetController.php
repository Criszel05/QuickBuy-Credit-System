<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $assignedAssets = Asset::where('status', 'Assigned')->count();
        $maintenanceAssets = Asset::where('status', 'Under Maintenance')->count();

        return view('assets.index', compact(
            'assets',
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets'
        ));
    }

    public function create()
    {
        return view('assets.form', [
            'asset' => new Asset(),
            'pageTitle' => 'Add New Asset',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAsset($request);

        $validated['recorded_by'] = Auth::id();

        Asset::create($validated);

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset successfully added.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.form', [
            'asset' => $asset,
            'pageTitle' => 'Edit Asset',
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $this->validateAsset($request, $asset->id);

        $asset->update($validated);

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset information successfully updated.');
    }

    public function destroy(Asset $asset)
    {
        $relatedRecords = [
            'asset_assignments',
            'asset_transfers',
            'maintenance_records',
            'software_installations',
            'asset_disposals',
        ];

        foreach ($relatedRecords as $table) {
            $exists = DB::table($table)
                ->where('asset_id', $asset->id)
                ->exists();

            if ($exists) {
                return redirect()
                    ->route('assets.index')
                    ->with('error', 'This asset already has history records. Please change its status to Retired instead of deleting it.');
            }
        }

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset successfully deleted.');
    }

    private function validateAsset(Request $request, ?int $assetId = null): array
    {
        return $request->validate([
            'asset_tag' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assets', 'asset_tag')->ignore($assetId),
            ],
            'asset_name' => ['required', 'string', 'max:255'],
            'asset_type' => ['required', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', Rule::in(['Good', 'Fair', 'Poor', 'For Repair'])],
            'status' => ['required', Rule::in(['Available', 'Assigned', 'Under Maintenance', 'Retired'])],
            'description' => ['nullable', 'string'],
        ]);
    }
}