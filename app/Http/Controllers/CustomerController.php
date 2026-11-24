<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalCustomers = Customer::where('is_active', true)->count();

        $inactiveCustomers = Customer::where('is_active', false)->count();

        $totalCreditLimit = Customer::where('is_active', true)
            ->sum('credit_limit');

        return view('customers.index', compact(
            'customers',
            'search',
            'totalCustomers',
            'inactiveCustomers',
            'totalCreditLimit'
        ));
    }

    public function create()
    {
        return view('customers.form', [
            'customer' => new Customer()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => [
                'required',
                'string',
                'max:255',
                'unique:customers,customer_code',
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['is_active'] = true;

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'customer_code')->ignore($customer->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // Deactivate instead of deleting customer records.
        $customer->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deactivated successfully.');
    }

    public function activate(Customer $customer)
    {
        $customer->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer activated successfully.');
    }
}
