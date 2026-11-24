<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // BUSINESS OVERVIEW
        $totalSalesToday = DB::table('sales')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $totalProducts = DB::table('products')
            ->where('is_active', true)
            ->count();

        $pendingCredits = DB::table('credits')
            ->where('balance', '>', 0)
            ->sum('balance');

        $totalAssets = DB::table('assets')->count();

        // ADDITIONAL COUNTS
        $totalCustomers = DB::table('customers')
            ->where('is_active', true)
            ->count();

        $totalEmployees = DB::table('employees')
            ->where('is_active', true)
            ->count();

        $lowStockProducts = DB::table('products')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();

        $overdueCredits = DB::table('credits')
            ->where('balance', '>', 0)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();

        // RECENT SALES
        $recentSales = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.invoice_number',
                'sales.total_amount',
                'sales.payment_method',
                'sales.created_at',
                'customers.name as customer_name'
            )
            ->orderByDesc('sales.created_at')
            ->limit(5)
            ->get();

        // RECENT CUSTOMER CREDITS
        $recentCredits = DB::table('credits')
            ->join('customers', 'credits.customer_id', '=', 'customers.id')
            ->select(
                'credits.credit_number',
                'credits.total_amount',
                'credits.paid_amount',
                'credits.balance',
                'credits.due_date',
                'credits.status',
                'customers.name as customer_name'
            )
            ->orderByDesc('credits.created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalSalesToday',
            'totalProducts',
            'pendingCredits',
            'totalAssets',
            'totalCustomers',
            'totalEmployees',
            'lowStockProducts',
            'overdueCredits',
            'recentSales',
            'recentCredits'
        ));
    }
}
