<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php
        $siteName = settings('agency_name', config('app.name', 'TKBOR'));
        // Inline @section values are already escaped; decode so {{ }} below doesn't double-escape (&amp;amp;).
        $pageTitle = html_entity_decode(trim($__env->yieldContent('title', settings('meta_title', settings('site_title', $siteName)))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $pageDescription = html_entity_decode(trim($__env->yieldContent('meta_description', settings('meta_description', settings('site_tagline', '')))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Priority order for social / meta share image:
        // 1. Page-specific featured image
        // 2. Header logo
        // 3. Existing site fallback image
        $pageSpecificImage = trim($__env->yieldContent('meta_image', $__env->yieldContent('og_image', '')));
        if ($pageSpecificImage !== '') {
            $metaImage = preg_match('#^https?://#i', $pageSpecificImage) ? $pageSpecificImage : uploaded_url($pageSpecificImage, asset($pageSpecificImage));
        } elseif (settings('site_logo')) {
            $metaImage = uploaded_url(settings('site_logo'));
        } elseif (settings('agency_logo')) {
            $metaImage = uploaded_url(settings('agency_logo'));
        } elseif (settings('og_image')) {
            $metaImage = uploaded_url(settings('og_image'));
        } else {
            $metaImage = asset('assets/images/resources/logo-1.png');
        }

        $ogType = trim($__env->yieldContent('og_type', 'website'));
        $canonicalUrl = request()->routeIs('home2') ? route('home') : (request()->integer('page') > 1 ? url()->current().'?page='.request()->integer('page') : url()->current());
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}" />
    @if (settings('meta_keywords'))
        <meta name="keywords" content="{{ settings('meta_keywords') }}" />
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    <meta name="robots" content="{{ request()->routeIs('search', 'error404') ? 'noindex, follow' : 'index, follow' }}" />

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $pageDescription }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $metaImage }}" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="{{ $pageDescription }}" />
    <meta name="twitter:image" content="{{ $metaImage }}" />

    @if (settings('google_site_verification'))
        <meta name="google-site-verification" content="{{ settings('google_site_verification') }}" />
    @endif

    @php $favicon = settings('site_favicon'); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ uploaded_url($favicon) }}" />
        <link rel="apple-touch-icon" href="{{ uploaded_url($favicon) }}" />
    @else
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" />
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" />
    @endif
    <link rel="manifest" href="{{ asset('assets/images/favicons/site.webmanifest') }}" />

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $metaImage,
        'email' => settings('contact_email', settings('agency_email')) ?: null,
        'telephone' => settings('contact_phone', settings('agency_phone')) ?: null,
        'address' => settings('contact_address', settings('agency_address')) ? [
            '@type' => 'PostalAddress',
            'streetAddress' => settings('contact_address', settings('agency_address')),
        ] : null,
        'sameAs' => array_values(array_filter([
            settings('facebook_url'), settings('instagram_url'), settings('linkedin_url'),
            settings('twitter_url'), settings('youtube_url'),
        ])),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>

    @stack('schema')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/search?q={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @if (!request()->routeIs('home', 'home2'))
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $pageTitle,
                'item' => url()->current(),
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    @if (settings('google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ settings('google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', {{ Illuminate\Support\Js::from(settings('google_analytics_id')) }});
        </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-select/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/aos/css/aos.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/misiom-icons/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/css/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/misiom.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('assets/css/misiom-fixes.css') }}" />
    @stack('styles')
</head>

<body class="custom-cursor">

    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="preloader">
        <div class="preloader__image" style="background-image: url({{ asset('assets/images/loader.png') }});"></div>
    </div>

    <a class="skip-link" href="#smooth-content">Skip to content</a>
    <div class="page-wrapper">
        @include('frontend.partials.header')

        @yield('content')

        @include('frontend.partials.footer')

    @include('frontend.partials.scripts')
    @stack('scripts')
</body>

</html>
