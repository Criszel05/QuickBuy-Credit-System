
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'QuickBuy Management System')</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f7f1e8;
            color: #4b2e20;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #4b2e20;
            color: #fff;
            padding: 25px 16px;
            flex-shrink: 0;
        }

        .brand {
            text-align: center;
            padding: 10px 5px 30px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            margin-bottom: 25px;
        }

        .brand h2 {
            font-size: 25px;
            letter-spacing: 1px;
            color: #f7f1e8;
        }

        .brand p {
            font-size: 11px;
            color: #d9c4b1;
            margin-top: 7px;
        }

        .menu-label {
            font-size: 11px;
            color: #c5a990;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 22px 10px 10px;
        }

        .sidebar a {
            display: block;
            color: #eadfd4;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 5px;
            font-size: 14px;
            transition: .2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #8b6246;
            color: #fff;
        }

        .sidebar .logout-button {
            width: 100%;
            border: none;
            background: #713c2c;
            color: white;
            padding: 12px;
            border-radius: 7px;
            margin-top: 25px;
            cursor: pointer;
            font-size: 14px;
        }

        .sidebar .logout-button:hover {
            background: #96533c;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            gap: 15px;
        }

        .topbar h3 {
            font-size: 17px;
        }

        .topbar .user-info {
            font-size: 13px;
            color: #8b6246;
        }

        .content {
            padding: 30px;
            flex: 1;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .page-header h1 {
            font-size: 27px;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #8b7565;
            font-size: 14px;
        }

        /* CARDS */
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 2px 10px rgba(75,46,32,.06);
        }

        .card h3 {
            margin-bottom: 18px;
            font-size: 18px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #fff;
            padding: 22px;
            border-radius: 10px;
            border-left: 5px solid #8b6246;
            box-shadow: 0 2px 10px rgba(75,46,32,.06);
        }

        .stat-card span {
            display: block;
            font-size: 13px;
            color: #8b7565;
            margin-bottom: 12px;
        }

        .stat-card h2 {
            font-size: 25px;
            color: #4b2e20;
        }

        /* BUTTONS */
        .btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            text-align: center;
        }

        .btn-primary {
            background: #4b2e20;
            color: #fff;
        }

        .btn-primary:hover {
            background: #70472f;
        }

        .btn-secondary {
            background: #e9ded2;
            color: #4b2e20;
        }

        .btn-secondary:hover {
            background: #d8c7b8;
        }

        .btn-danger {
            background: #a94442;
            color: #fff;
        }

        .btn-success {
            background: #537b50;
            color: #fff;
        }

        .btn-warning {
            background: #c28a35;
            color: #fff;
        }

        /* TABLES */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        table th {
            background: #f1e7dc;
            color: #4b2e20;
            font-size: 13px;
            text-align: left;
            padding: 14px;
        }

        table td {
            padding: 13px 14px;
            border-bottom: 1px solid #eee5dc;
            font-size: 13px;
            color: #5e493b;
        }

        table tr:hover td {
            background: #fdfaf7;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        /* FORMS */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #d8c7b8;
            border-radius: 6px;
            outline: none;
            background: #fff;
            color: #4b2e20;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #8b6246;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        /* ALERTS */
        .alert {
            padding: 14px 18px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #e4f2e2;
            color: #315d30;
            border: 1px solid #c5dfc1;
        }

        .alert-error {
            background: #f8e5e3;
            color: #9c302c;
            border: 1px solid #ecc7c4;
        }

        .alert ul {
            margin: 8px 0 0 20px;
        }

        /* STATUS BADGES */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-success {
            background: #e4f2e2;
            color: #315d30;
        }

        .status-warning {
            background: #fff0d6;
            color: #98621a;
        }

        .status-danger {
            background: #f8e5e3;
            color: #9c302c;
        }

        /* SEARCH AND PAGINATION */
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-form input,
        .search-form select {
            flex: 1;
            min-width: 180px;
            padding: 10px;
            border: 1px solid #d8c7b8;
            border-radius: 6px;
        }

        .pagination-wrapper {
            margin-top: 20px;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* POS */
        .pos-total {
            max-width: 350px;
            margin: 25px 0 25px auto;
        }

        .pos-total div {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
        }

        .pos-total .grand-total {
            border-top: 2px solid #8b6246;
            font-size: 20px;
            color: #4b2e20;
        }

        .btn-large {
            width: 100%;
            padding: 14px;
            font-size: 16px;
        }

        /* INVOICE */
        .invoice-card {
            max-width: 850px;
            margin: 0 auto;
            padding: 30px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .invoice-header h1 {
            color: #4b2e20;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-customer {
            margin: 25px 0;
        }

        .invoice-customer p {
            margin: 7px 0;
        }

        .invoice-totals {
            max-width: 350px;
            margin: 25px 0 0 auto;
        }

        .invoice-totals p {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 7px 0;
        }

        .invoice-grand-total {
            border-top: 2px solid #8b6246;
            padding-top: 12px;
            font-size: 20px;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 35px;
            color: #8b6246;
        }

        /* FOOTER */
        .footer {
            padding: 18px 30px;
            background: #fff;
            color: #9b8879;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #eee5dc;
        }

        /* MOBILE */
        @media(max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .content {
                padding: 20px;
            }
        }

        @media(max-width: 650px) {
            .app-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px;
            }

            .brand {
                padding-bottom: 15px;
                margin-bottom: 12px;
            }

            .sidebar a {
                display: inline-block;
                margin: 3px;
                padding: 9px 12px;
            }

            .menu-label {
                margin-top: 14px;
            }

            .topbar {
                padding: 15px;
            }

            .content {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 23px;
            }
        }

        /* PRINT */
        @media print {
            .sidebar,
            .topbar,
            .footer,
            .no-print {
                display: none !important;
            }

            .content {
                padding: 0;
            }

            body {
                background: white;
            }

            .invoice-card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="app-container">

    @auth

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <h2>QuickBuy</h2>
            <p>Management System</p>
        </div>

        <!-- MAIN MENU -->
        <div class="menu-label">Main Menu</div>

        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <!-- INVENTORY MANAGEMENT -->
        <div class="menu-label">Inventory Management</div>

        <a href="{{ route('products.index') }}"
           class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            Products
        </a>

        <a href="{{ route('categories.index') }}"
           class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
            Categories
        </a>

        <a href="{{ route('inventory.index') }}"
           class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            Inventory
        </a>

        <a href="{{ route('customers.index') }}"
           class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
            Customers
        </a>

        <!-- SALES MANAGEMENT -->
        <div class="menu-label">Sales Management</div>

        <a href="{{ route('pos.create') }}"
           class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
            Point of Sale
        </a>

        <a href="{{ route('sales.index') }}"
           class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
            Sales History
        </a>

        <!-- CREDIT MANAGEMENT -->
        <div class="menu-label">Credit Management</div>

        <a href="{{ route('credits.index') }}"
           class="{{ request()->routeIs('credits.*') ? 'active' : '' }}">
            Credit / Utang
        </a>

        <a href="{{ route('payments.index') }}"
           class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
            Payments
        </a>

        
        <!-- ASSET MANAGEMENT -->
        <div class="menu-label">Asset Management</div>

        <a href="{{ route('assets.index') }}"
           class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">
            <span>📦</span>
            <span>Asset Records</span>
        </a>

        <a href="{{ route('asset-assignments.index') }}"
           class="{{ request()->routeIs('asset-assignments.*') ? 'active' : '' }}">
            <span>🔄</span>
            <span>Asset Assignment & Transfer</span>
        </a>

        <a href="{{ route('maintenance.index') }}"
           class="{{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
             <span>🛠️</span>
             <span>Maintenance</span>
        </a>

        <a href="{{ route('software-licenses.index') }}"
          class="{{ request()->routeIs('software-licenses.*') ? 'active' : '' }}">
             <span>💻</span>
             <span>Software Licenses</span>
        </a>

        <a href="{{ route('asset-disposals.index') }}"
          class="{{ request()->routeIs('asset-disposals.*') ? 'active' : '' }}">
             <span>🗑️</span>
             <span>Asset Disposal</span>
        </a>

        <!-- EMPLOYEE MANAGEMENT -->
        <div class="menu-label">Employee Management</div>

        <a href="{{ route('employees.index') }}"
           class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <span>👥</span>
            <span>Employees</span>
        </a>

           <!-- REPORTS -->
        <div class="menu-label">Reports</div>

        <a href="{{ route('reports.index') }}"
            class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
             <span>📊</span>
             <span>Reports Dashboard</span>
        </a>

        <!-- LOGOUT -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">
                Logout
            </button>
        </form>

    </aside>

    @endauth

    <!-- MAIN CONTENT -->
    <main class="main-content">

        @auth
        <header class="topbar">
            <h3>@yield('header', 'QuickBuy Management System')</h3>

            <div class="user-info">
                Welcome,
                <strong>{{ auth()->user()->name }}</strong>
            </div>
        </header>
        @endauth

        <!-- PAGE CONTENT -->
        <section class="content">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </section>

        <!-- FOOTER -->
        <footer class="footer">
            QuickBuy Management System &copy; {{ date('Y') }}
            All Rights Reserved.
        </footer>

    </main>

</div>

@stack('scripts')

</body>
</html>
