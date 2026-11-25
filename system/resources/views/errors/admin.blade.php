@php
    $agencyName = settings('agency_name', config('app.name'));
    $agencyLogo = settings('agency_logo');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>{{ $code }} — {{ $title }}</title>
    @if (settings('site_favicon'))<link rel="icon" href="{{ uploaded_url(settings('site_favicon')) }}" />@endif
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/admin/css/admin-modules.css') }}" />
</head>
<body class="admin-body admin-error-body">

    <main class="admin-error">
        <div class="admin-error__mark">
            @if ($agencyLogo)
                <img src="{{ uploaded_url($agencyLogo) }}" alt="{{ $agencyName }}">
            @else
                <i class="fas fa-triangle-exclamation"></i>
            @endif
        </div>

        <p class="admin-error__code">{{ $code }}</p>
        <h1 class="admin-error__title">{{ $title }}</h1>
        <p class="admin-error__text">{{ $message }}</p>

        <div class="admin-error__actions">
            @auth('admin')
                <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--primary"><i class="fas fa-gauge-high"></i> Back to Dashboard</a>
            @else
                <a href="{{ route('admin.login') }}" class="admin-btn admin-btn--primary"><i class="fas fa-right-to-bracket"></i> Sign In</a>
            @endauth
            <a href="{{ url('/') }}" class="admin-btn admin-btn--ghost"><i class="fas fa-globe"></i> Visit Website</a>
        </div>
    </main>

</body>
</html>
