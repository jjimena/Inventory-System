<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

</head>

<body>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white"
            href="{{ route('dashboard.index') }}">Phoenix Super LPG - Inventory System</a>

        <button class="navbar-toggler d-md-none px-3 text-white" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>

        <div id="navbarSearch" class="navbar-search w-100 collapse">
            <input class="form-control w-150 rounded-0 border-0" type="text" placeholder="Search"
                aria-label="Search">
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar: Offcanvas on small screens, inline on larger screens -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar offcanvas-lg offcanvas-start"
                tabindex="-1">
                {{-- <div class="offcanvas-header d-md-none">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div> --}}
                <div class="offcanvas-header d-md-none">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="position-sticky pt-3">
                    <!-- Logo Section -->
                    <div class="mb-3 text-left d-flex align-items-center">
                        <a href="{{ route('dashboard.index') }}" style="text-decoration: none; color: black;">
                            <img src="{{ asset('images/lpglogo.jpg') }}" alt="Company Logo"
                                class="rounded-circle border"
                                style="width: 39px; height: 39px; border: 2px solid #000;">
                            <strong style="padding: 20px">{{ auth()->user()->name }}</strong>
                        </a>
                    </div>

                    <div class="border-bottom mb-3"> </div>

                    <ul class="nav flex-column">
                        @if (auth()->user()->role_id !== App\Models\Role::HUB)
                            <li class="nav-item">
                                <a class="nav-link" id="nav-dashboard" href="{{ route('dashboard.index') }}">
                                    <i class="bi bi-house-door"></i>
                                    Dashboard
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
                            <li class="nav-item">
                                <a class="nav-link" id="nav-categories"
                                    href="{{ route('dashboard.categories.index') }}">
                                    <i class="bi bi-tags"></i> <!-- Updated icon -->
                                    Product Categories
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" id="nav-products" href="{{ route('dashboard.products.index') }}">
                                <i class="bi bi-bag"></i>
                                Products
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="nav-purchase" href="{{ route('dashboard.order-items.create') }}">
                                <i class="bi bi-cart-plus"></i> <!-- Updated icon -->
                                New Order
                            </a>
                        </li>

                        @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
                            <li class="nav-item">
                                <a class="nav-link" id="nav-orders" href="{{ route('dashboard.orders.index') }}">
                                    <i class="bi bi-person-check"></i> <!-- Updated icon -->
                                    Customer
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" id="nav-reports_form"
                                href="{{ route('dashboard.order-items.index') }}">
                                <i class="bi bi-clock-history"></i> <!-- Updated icon -->
                                Purchase History
                            </a>
                        </li>

                        @if (auth()->user()->role_id === App\Models\Role::ADMIN || auth()->user()->role_id === App\Models\Role::STAFF)
                            <li class="nav-item">
                                <a class="nav-link" id="nav-order-items"
                                    href="{{ route('dashboard.reports.report_form') }}">
                                    <i class="bi bi-graph-up"></i> <!-- Updated icon -->
                                    Generate Reports
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->role_id === App\Models\Role::ADMIN)
                            <h6
                                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>ADMIN</span>
                                <i class="bi bi-wrench-adjustable"></i>
                            </h6>
                            <ul class="nav flex-column mb-2">
                                <li class="nav-item">
                                    <a class="nav-link" id="nav-users" href="{{ route('dashboard.users.index') }}">
                                        <i class="bi bi-person-lines-fill"></i> <!-- Updated icon -->
                                        Users
                                    </a>
                                </li>
                            </ul>
                        @endif

                        <li class="nav-item mt-3">
                            <a class="nav-link" id="nav-profile" href="{{ route('dashboard.profiles.show') }}">
                                <i class="bi bi-person-fill"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link logout-link">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Sign out
                                </button>
                            </form>
                        </li>
                    </ul>

                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title-header')</h1>
                </div>

                <div class="col-lg-12 mt-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
