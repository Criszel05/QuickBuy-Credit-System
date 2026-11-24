<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesQuery = DB::table('sales');

        if ($startDate) {
            $salesQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $salesQuery->whereDate('created_at', '<=', $endDate);
        }

        $totalSales = (clone $salesQuery)->sum('total_amount');
        $salesCount = (clone $salesQuery)->count();

        $totalProducts = DB::table('products')
            ->where('is_active', true)
            ->count();

        $totalStock = DB::table('products')
            ->where('is_active', true)
            ->sum('stock_quantity');

        $lowStock = DB::table('products')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();

        $outstandingCredits = DB::table('credits')
            ->where('balance', '>', 0)
            ->sum('balance');

        $totalCustomers = DB::table('customers')
            ->where('is_active', true)
            ->count();

        $totalEmployees = DB::table('employees')
            ->where('is_active', true)
            ->count();

        $totalAssets = DB::table('assets')->count();

        $availableAssets = DB::table('assets')
            ->where('status', 'Available')
            ->count();

        $assignedAssets = DB::table('assets')
            ->where('status', 'Assigned')
            ->count();

        $maintenanceAssets = DB::table('assets')
            ->where('status', 'Under Maintenance')
            ->count();

        $retiredAssets = DB::table('assets')
            ->where('status', 'Retired')
            ->count();

        $maintenancePending = DB::table('maintenance_records')
            ->where('status', 'Pending')
            ->count();

        $maintenanceInProgress = DB::table('maintenance_records')
            ->where('status', 'In Progress')
            ->count();

        $maintenanceCompleted = DB::table('maintenance_records')
            ->where('status', 'Completed')
            ->count();

        $totalLicenses = DB::table('software_licenses')->count();

        $expiredLicenses = DB::table('software_licenses')
            ->whereDate('expiration_date', '<', now()->toDateString())
            ->count();

        $expiringLicenses = DB::table('software_licenses')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '>=', now()->toDateString())
            ->whereDate('expiration_date', '<=', now()->addDays(30)->toDateString())
            ->count();

        $totalDisposals = DB::table('asset_disposals')->count();

        $pendingDisposals = DB::table('asset_disposals')
            ->where('status', 'Pending')
            ->count();

        $completedDisposals = DB::table('asset_disposals')
            ->where('status', 'Completed')
            ->count();

        $disposalValue = DB::table('asset_disposals')
            ->where('status', 'Completed')
            ->sum('disposal_value');

        $recentSalesQuery = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.invoice_number',
                'sales.total_amount',
                'sales.payment_method',
                'sales.created_at',
                'customers.name as customer_name'
            );

        if ($startDate) {
            $recentSalesQuery->whereDate('sales.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $recentSalesQuery->whereDate('sales.created_at', '<=', $endDate);
        }

        $recentSales = $recentSalesQuery
            ->orderByDesc('sales.created_at')
            ->limit(10)
            ->get();

        $recentDisposals = DB::table('asset_disposals')
            ->join('assets', 'asset_disposals.asset_id', '=', 'assets.id')
            ->select(
                'assets.asset_tag',
                'assets.asset_name',
                'asset_disposals.disposal_method',
                'asset_disposals.status',
                'asset_disposals.disposal_value',
                'asset_disposals.retirement_date'
            )
            ->orderByDesc('asset_disposals.created_at')
            ->limit(10)
            ->get();

        $assetStatusSummary = DB::table('assets')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'salesCount',
            'totalProducts',
            'totalStock',
            'lowStock',
            'outstandingCredits',
            'totalCustomers',
            'totalEmployees',
            'totalAssets',
            'availableAssets',
            'assignedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'maintenancePending',
            'maintenanceInProgress',
            'maintenanceCompleted',
            'totalLicenses',
            'expiredLicenses',
            'expiringLicenses',
            'totalDisposals',
            'pendingDisposals',
            'completedDisposals',
            'disposalValue',
            'recentSales',
            'recentDisposals',
            'assetStatusSummary'
        ));
    }
}
