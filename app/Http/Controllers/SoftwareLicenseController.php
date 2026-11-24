<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\SoftwareInstallation;
use App\Models\SoftwareLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SoftwareLicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = SoftwareLicense::withCount([
            'installations as installed_count' => function ($q) {
                $q->where('status', 'Installed');
            }
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('software_name', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('license_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $licenses = $query->latest()->paginate(10)->withQueryString();

        $totalLicenses = SoftwareLicense::count();
        $activeLicenses = SoftwareLicense::where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('expiration_date')
                    ->orWhereDate('expiration_date', '>=', today());
            })->count();

        $expiredLicenses = SoftwareLicense::where(function ($q) {
            $q->where('status', 'Expired')
                ->orWhereDate('expiration_date', '<', today());
        })->count();

        $totalInstallations = SoftwareInstallation::where('status', 'Installed')->count();

        return view('software-licenses.index', compact(
            'licenses',
            'totalLicenses',
            'activeLicenses',
            'expiredLicenses',
            'totalInstallations'
        ));
    }

    public function create()
    {
        return view('software-licenses.form', [
            'license' => new SoftwareLicense(),
            'pageTitle' => 'Add Software License',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'software_name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'license_key' => ['nullable', 'string', 'max:1000'],
            'license_type' => ['required', 'string', 'max:100'],
            'number_of_licenses' => ['required', 'integer', 'min:1'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['status'] = !empty($validated['expiration_date'])
            && $validated['expiration_date'] < today()->toDateString()
                ? 'Expired'
                : 'Active';

        SoftwareLicense::create($validated);

        return redirect()->route('software-licenses.index')
            ->with('success', 'Software license successfully added.');
    }

    public function edit(SoftwareLicense $softwareLicense)
    {
        return view('software-licenses.form', [
            'license' => $softwareLicense,
            'pageTitle' => 'Edit Software License',
        ]);
    }

    public function update(Request $request, SoftwareLicense $softwareLicense)
    {
        $validated = $request->validate([
            'software_name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'license_key' => ['nullable', 'string', 'max:1000'],
            'license_type' => ['required', 'string', 'max:100'],
            'number_of_licenses' => ['required', 'integer', 'min:1'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Expired'])],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $installedCount = $softwareLicense->installations()
            ->where('status', 'Installed')
            ->count();

        if ($validated['number_of_licenses'] < $installedCount) {
            return back()->withErrors([
                'number_of_licenses' => "There are currently {$installedCount} active installations. The license quantity cannot be lower than this."
            ])->withInput();
        }

        if (
            !empty($validated['expiration_date'])
            && $validated['expiration_date'] < today()->toDateString()
        ) {
            $validated['status'] = 'Expired';
        } elseif (
            $validated['status'] === 'Expired'
            && (
                empty($validated['expiration_date'])
                || $validated['expiration_date'] >= today()->toDateString()
            )
        ) {
            $validated['status'] = 'Active';
        }

        $softwareLicense->update($validated);

        return redirect()->route('software-licenses.index')
            ->with('success', 'Software license successfully updated.');
    }

    public function installations(SoftwareLicense $softwareLicense)
    {
        $softwareLicense->load([
            'installations.asset'
        ]);

        $assets = Asset::where('status', '!=', 'Retired')
            ->orderBy('asset_name')
            ->get();

        $installedCount = $softwareLicense->installations()
            ->where('status', 'Installed')
            ->count();

        $availableCount = max(
            0,
            $softwareLicense->number_of_licenses - $installedCount
        );

        return view('software-licenses.installations', compact(
            'softwareLicense',
            'assets',
            'installedCount',
            'availableCount'
        ));
    }

    public function install(Request $request, SoftwareLicense $softwareLicense)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'installation_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $softwareLicense) {
            $license = SoftwareLicense::whereKey($softwareLicense->id)
                ->lockForUpdate()
                ->firstOrFail();

            $asset = Asset::whereKey($validated['asset_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($license->status !== 'Active') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'This software license is not active.',
                ]);
            }

            if (
                $license->expiration_date
                && $license->expiration_date->lt(today())
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'This software license has expired.',
                ]);
            }

            if ($asset->status === 'Retired') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'Software cannot be installed on a retired asset.',
                ]);
            }

            $alreadyInstalled = SoftwareInstallation::where('software_license_id', $license->id)
                ->where('asset_id', $asset->id)
                ->where('status', 'Installed')
                ->exists();

            if ($alreadyInstalled) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'This software is already installed on the selected asset.',
                ]);
            }

            $installedCount = SoftwareInstallation::where('software_license_id', $license->id)
                ->where('status', 'Installed')
                ->count();

            if ($installedCount >= $license->number_of_licenses) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asset_id' => 'No available license slots remain.',
                ]);
            }

            SoftwareInstallation::create([
                'software_license_id' => $license->id,
                'asset_id' => $asset->id,
                'installation_date' => $validated['installation_date'],
                'status' => 'Installed',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('software-licenses.installations', $softwareLicense)
            ->with('success', 'Software successfully installed on the asset.');
    }

    public function uninstall(
        SoftwareLicense $softwareLicense,
        SoftwareInstallation $installation
    ) {
        abort_unless(
            $installation->software_license_id === $softwareLicense->id,
            404
        );

        if ($installation->status !== 'Installed') {
            return back()->with('error', 'This installation is already uninstalled.');
        }

        $installation->update([
            'status' => 'Uninstalled',
            'uninstallation_date' => today(),
        ]);

        return back()->with('success', 'Software successfully uninstalled.');
    }
}
