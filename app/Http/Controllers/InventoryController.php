<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalProducts = Product::where('is_active', true)->count();

        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();

        $totalStock = Product::where('is_active', true)
            ->sum('stock_quantity');

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('inventory.index', compact(
            'products',
            'search',
            'totalProducts',
            'lowStockCount',
            'totalStock',
            'recentMovements'
        ));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'movement_type' => ['required', 'in:stock_in,stock_out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated) {

            $product = Product::whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!$product->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is inactive and cannot be updated.',
                ]);
            }

            $previousStock = (int) $product->stock_quantity;
            $inputQuantity = (int) $validated['quantity'];

            if ($validated['movement_type'] === 'stock_in') {
                $newStock = $previousStock + $inputQuantity;
                $recordedQuantity = $inputQuantity;
            } elseif ($validated['movement_type'] === 'stock_out') {

                if ($inputQuantity > $previousStock) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Insufficient stock. Available quantity: ' . $previousStock,
                    ]);
                }

                $newStock = $previousStock - $inputQuantity;
                $recordedQuantity = $inputQuantity;

            } else {
                // For adjustment, quantity represents the actual counted stock.
                $newStock = $inputQuantity;
                $recordedQuantity = abs($newStock - $previousStock);
            }

            $product->update([
                'stock_quantity' => $newStock,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'movement_type' => $validated['movement_type'],
                'quantity' => $recordedQuantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        });

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function history(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('movement_type', $request->input('type'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inventory.history', compact('movements'));
    }

    public function lowStock()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->orderBy('stock_quantity')
            ->paginate(15);

        return view('inventory.low-stock', compact('products'));
    }
}
