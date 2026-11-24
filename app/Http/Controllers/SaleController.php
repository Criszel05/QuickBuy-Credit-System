<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $customers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,credit'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($validated) {
            $customer = null;

            if (!empty($validated['customer_id'])) {
                $customer = Customer::where('is_active', true)
                    ->find($validated['customer_id']);

                if (!$customer) {
                    throw ValidationException::withMessages([
                        'customer_id' => 'The selected customer is not active.',
                    ]);
                }
            }

            $subtotal = 0;
            $cartItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('is_active', true)
                    ->lockForUpdate()
                    ->find($item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => 'One of the selected products is unavailable.',
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if ($product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}. Available stock: {$product->stock_quantity}.",
                    ]);
                }

                $lineTotal = (float) $product->price * $quantity;
                $subtotal += $lineTotal;

                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $lineTotal,
                ];
            }

            $discount = (float) ($validated['discount'] ?? 0);

            if ($discount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount' => 'Discount cannot be greater than the subtotal.',
                ]);
            }

            $total = $subtotal - $discount;
            $paymentMethod = $validated['payment_method'];

            if ($paymentMethod === 'cash') {
                $amountPaid = (float) ($validated['amount_paid'] ?? 0);

                if ($amountPaid < $total) {
                    throw ValidationException::withMessages([
                        'amount_paid' => 'The cash received must be equal to or greater than the total amount.',
                    ]);
                }

                $change = $amountPaid - $total;
                $saleStatus = 'completed';
            } else {
                if (!$customer) {
                    throw ValidationException::withMessages([
                        'customer_id' => 'Please select a customer for a credit sale.',
                    ]);
                }

                $existingBalance = (float) Credit::where('customer_id', $customer->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->sum('balance');

                $creditLimit = (float) $customer->credit_limit;

                if (($existingBalance + $total) > $creditLimit) {
                    throw ValidationException::withMessages([
                        'customer_id' => 'This sale exceeds the customer credit limit. Existing balance: ₱'
                            . number_format($existingBalance, 2)
                            . '. Credit limit: ₱'
                            . number_format($creditLimit, 2) . '.',
                    ]);
                }

                $amountPaid = 0;
                $change = 0;
                $saleStatus = 'credit';
            }

            $sale = Sale::create([
                'invoice_number' => 'QB-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'customer_id' => $customer?->id,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'amount_paid' => $amountPaid,
                'change_amount' => $change,
                'status' => $saleStatus,
            ]);

            foreach ($cartItems as $item) {
                $product = $item['product'];
                $previousStock = $product->stock_quantity;
                $newStock = $previousStock - $item['quantity'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update([
                    'stock_quantity' => $newStock,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'movement_type' => 'stock_out',
                    'quantity' => $item['quantity'],
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'remarks' => 'Sold under invoice ' . $sale->invoice_number,
                ]);
            }

            if ($paymentMethod === 'credit') {
                Credit::create([
                    'credit_number' => 'CR-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                    'customer_id' => $customer->id,
                    'sale_id' => $sale->id,
                    'total_amount' => $total,
                    'paid_amount' => 0,
                    'balance' => $total,
                    'credit_date' => now()->toDateString(),
                    'due_date' => null,
                    'status' => 'unpaid',
                    'notes' => 'Created from invoice ' . $sale->invoice_number,
                ]);
            }

            return $sale;
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Sale recorded successfully.');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $sales = Sale::with(['customer', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalSales = Sale::where('status', 'completed')->sum('total_amount');
        $totalTransactions = Sale::count();
        $creditSales = Sale::where('payment_method', 'credit')->sum('total_amount');

        return view('sales.index', compact(
            'sales',
            'search',
            'totalSales',
            'totalTransactions',
            'creditSales'
        ));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items']);

        return view('sales.show', compact('sale'));
    }
}
