@php
    $agencyName = settings('agency_name', config('app.name'));
    // The login card is dark, so the light artwork wins when present.
    $agencyLogo = settings('agency_logo_light') ?: settings('agency_logo');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('page-title', 'Admin Login') - {{ $agencyName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin-login.css') }}" />
</head>
<body class="login-body">

    {{-- Decorative background: gold light ribbons over dark folds. --}}
    <div class="login-bg" aria-hidden="true">
        <span class="login-ribbon login-ribbon--one"></span>
        <span class="login-ribbon login-ribbon--two"></span>
        <span class="login-ribbon login-ribbon--three"></span>
        <span class="login-fold login-fold--one"></span>
        <span class="login-fold login-fold--two"></span>
        <span class="login-fold login-fold--three"></span>
    </div>

    <main class="login-card">
        <div class="login-brand">
            <div class="login-brand__mark {{ $agencyLogo ? 'login-brand__mark--logo' : '' }}">
                @if ($agencyLogo)
                    <img src="{{ uploaded_url($agencyLogo) }}" alt="{{ $agencyName }}">
                @else
                    {{ strtoupper(substr($agencyName, 0, 1)) }}
                @endif
            </div>
            {{-- The lockup already carries the name; only spell it out when there is no artwork. --}}
            @unless ($agencyLogo)
                <h1 class="login-brand__name">{{ $agencyName }}</h1>
            @endunless
            <p class="login-brand__tagline">@yield('tagline', 'Welcome back! Please login to continue')</p>
        </div>

        @if ($errors->any())
            <div class="login-alert login-alert--error">
                <i class="fas fa-circle-exclamation" style="margin-top:2px;"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="login-alert login-alert--success">
                <i class="fas fa-circle-check" style="margin-top:2px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @yield('form')

        <div class="login-help">
            @yield('help', 'Trouble signing in? Contact your system administrator.')
        </div>

        <p class="login-foot">&copy; {{ date('Y') }} {{ $agencyName }}. All rights reserved.</p>
    </main>

    <script src="{{ versioned_asset('assets/admin/js/admin.js') }}"></script>
</body>
</html>
