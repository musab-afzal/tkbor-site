<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') - Admin - {{ settings('agency_name', config('app.name')) }}</title>
    @php $adminFavicon = settings('site_favicon') ?: settings('agency_logo'); @endphp
    @if ($adminFavicon)<link rel="icon" href="{{ uploaded_url($adminFavicon) }}" />@endif
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin-modules.css') }}" />
    @stack('styles')
</head>
<body class="admin-body">

    @include('admin.partials.flash')

    <div class="admin-shell">
        @include('admin.partials.sidebar')

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar__left">
                    <button type="button" class="admin-sidebar-toggle" data-sidebar-toggle aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="admin-topbar__title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="admin-topbar__right">
                    @include('admin.partials.notifications-bell')
                    <div class="admin-topbar__user">
                        <div class="admin-avatar">{{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}</div>
                        <div>
                            <div class="admin-topbar__user-name">{{ auth('admin')->user()->name ?? 'Admin' }}</div>
                            <div class="admin-topbar__user-role">{{ auth('admin')->user()?->role?->name ?? 'Administrator' }}</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-logout-btn">
                            <i class="fas fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ versioned_asset('assets/admin/js/admin.js') }}"></script>
    <script src="{{ versioned_asset('assets/admin/js/admin-modules.js') }}"></script>
    @stack('scripts')
</body>
</html>
