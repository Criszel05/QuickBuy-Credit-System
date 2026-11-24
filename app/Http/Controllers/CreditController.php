<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $credits = Credit::with('customer', 'sale')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('credit_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('customer_code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalCredit = Credit::sum('total_amount');
        $totalPaid = Credit::sum('paid_amount');
        $totalBalance = Credit::sum('balance');
        $unpaidCount = Credit::whereIn('status', ['unpaid', 'partial'])->count();

        return view('credits.index', compact(
            'credits',
            'search',
            'status',
            'totalCredit',
            'totalPaid',
            'totalBalance',
            'unpaidCount'
        ));
    }

    public function show(Credit $credit)
    {
        $credit->load([
            'customer',
            'sale.items',
            'payments.user'
        ]);

        return view('credits.show', compact('credit'));
    }
}
