<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SoftwareLicenseController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\ReportsController;

Route::redirect('/', '/login');

// GUEST ROUTES
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register']);
});

// AUTHENTICATED ROUTES

Route::middleware('auth')->group(function () {

Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
Route::get('/pos', [SaleController::class, 'create'])->name('pos.create');
// REPORTS DASHBOARD
Route::get('/reports', [ReportsController::class, 'index'])
    ->name('reports.index');
// ASSET DISPOSAL
Route::resource('asset-disposals', AssetDisposalController::class)
    ->parameters(['asset-disposals' => 'assetDisposal'])
    ->only(['index', 'create', 'store', 'edit', 'update']);
// SOFTWARE LICENSE MANAGEMENT
Route::resource('software-licenses', SoftwareLicenseController::class)
    ->parameters(['software-licenses' => 'softwareLicense'])
    ->only(['index', 'create', 'store', 'edit', 'update']);

Route::get(
    '/software-licenses/{softwareLicense}/installations',
    [SoftwareLicenseController::class, 'installations']
)->name('software-licenses.installations');

Route::post(
    '/software-licenses/{softwareLicense}/installations',
    [SoftwareLicenseController::class, 'install']
)->name('software-licenses.install');

Route::patch(
    '/software-licenses/{softwareLicense}/installations/{installation}/uninstall',
    [SoftwareLicenseController::class, 'uninstall']
)->name('software-licenses.uninstall');
// MAINTENANCE
Route::resource('maintenance', MaintenanceController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
// ASSET ASSIGNMENTS AND TRANSFERS
Route::get('/asset-assignments', [AssetAssignmentController::class, 'index'])
    ->name('asset-assignments.index');

Route::get('/asset-assignments/create', [AssetAssignmentController::class, 'create'])
    ->name('asset-assignments.create');

Route::post('/asset-assignments', [AssetAssignmentController::class, 'store'])
    ->name('asset-assignments.store');

Route::get('/asset-assignments/{assignment}/return', [AssetAssignmentController::class, 'returnForm'])
    ->name('asset-assignments.return-form');

Route::patch('/asset-assignments/{assignment}/return', [AssetAssignmentController::class, 'returnAsset'])
    ->name('asset-assignments.return');

Route::get('/asset-assignments/{assignment}/transfer', [AssetAssignmentController::class, 'transferForm'])
    ->name('asset-assignments.transfer-form');

Route::post('/asset-assignments/{assignment}/transfer', [AssetAssignmentController::class, 'transfer'])
    ->name('asset-assignments.transfer');
// EMPLOYEES
Route::resource('employees', EmployeeController::class)
    ->except(['show']);

Route::patch('/employees/{employee}/activate', [EmployeeController::class, 'activate'])
    ->name('employees.activate');


    // ASSET RECORDS
Route::resource('assets', AssetController::class)->except(['show']);

Route::post('/pos', [SaleController::class, 'store'])->name('pos.store');

Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');

Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
// CREDIT / UTANG
Route::get('/credits', [CreditController::class, 'index'])
    ->name('credits.index');

Route::get('/credits/{credit}', [CreditController::class, 'show'])
    ->name('credits.show');

// PAYMENTS
Route::get('/payments', [PaymentController::class, 'index'])
    ->name('payments.index');

Route::post('/credits/{credit}/payments', [PaymentController::class, 'store'])
    ->name('payments.store');

    // PRODUCTS
    Route::resource('products', ProductController::class)
        ->except(['show']);

    // CATEGORIES
    Route::resource('categories', CategoryController::class)
        ->except(['show']);
    
    // CUSTOMERS
    Route::resource('customers', CustomerController::class)
    ->except(['show']);

    Route::patch('/customers/{customer}/activate', [CustomerController::class, 'activate'])
    ->name('customers.activate');

    // INVENTORY
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');

    Route::get('/inventory/create', [InventoryController::class, 'create'])
        ->name('inventory.create');

    Route::post('/inventory', [InventoryController::class, 'store'])
        ->name('inventory.store');

    Route::get('/inventory/history', [InventoryController::class, 'history'])
        ->name('inventory.history');

    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])
        ->name('inventory.low-stock');
});
