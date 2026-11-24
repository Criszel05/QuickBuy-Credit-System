<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $inactiveEmployees = Employee::where('is_active', false)->count();

        return view('employees.index', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees'
        ));
    }

    public function create()
    {
        return view('employees.form', [
            'employee' => new Employee(),
            'pageTitle' => 'Add New Employee',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => [
                'required',
                'string',
                'max:100',
                'unique:employees,employee_code',
            ],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_active'] = true;

        Employee::create($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee successfully added.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.form', [
            'employee' => $employee,
            'pageTitle' => 'Edit Employee',
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employees', 'employee_code')->ignore($employee->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee->update($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee information successfully updated.');
    }

    public function destroy(Employee $employee)
    {
        // Deactivate instead of deleting so asset history is preserved.
        $employee->update(['is_active' => false]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee successfully deactivated.');
    }

    public function activate(Employee $employee)
    {
        $employee->update(['is_active' => true]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee successfully activated.');
    }
}
