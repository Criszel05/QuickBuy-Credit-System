<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $payments = Payment::with(['credit.customer', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('credit', function ($creditQuery) use ($search) {
                        $creditQuery->where('credit_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalPayments = Payment::sum('amount');
        $totalReceipts = Payment::count();

        return view('payments.index', compact(
            'payments',
            'search',
            'totalPayments',
            'totalReceipts'
        ));
    }

    public function store(Request $request, Credit $credit)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,gcash,bank_transfer,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $credit) {
            $lockedCredit = Credit::whereKey($credit->id)
                ->lockForUpdate()
                ->firstOrFail();

            $balance = (float) $lockedCredit->balance;
            $amount = (float) $validated['amount'];

            if ($lockedCredit->status === 'paid' || $balance <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'This credit has already been fully paid.',
                ]);
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot be greater than the remaining balance of ₱'
                        . number_format($balance, 2) . '.',
                ]);
            }

            $newPaidAmount = (float) $lockedCredit->paid_amount + $amount;
            $newBalance = max(0, $balance - $amount);

            Payment::create([
                'receipt_number' => 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'credit_id' => $lockedCredit->id,
                'user_id' => Auth::id(),
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $lockedCredit->update([
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'partial',
            ]);
        });

        return redirect()
            ->route('credits.show', $credit)
            ->with('success', 'Payment recorded successfully. Credit balance has been updated.');
    }
}
