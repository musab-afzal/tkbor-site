@extends('layouts.app')

@section('title', 'Pricing Plans - '.settings('agency_name', config('app.name')))
@section('meta_description', (string) ('Compare TKBOR pricing plans, from a single business website to a full custom platform with AI automation and marketing included.'))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Pricing Plans</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Packages</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="packages-one section-space">
    <div class="packages-one__bg" style="background-image: url({{ asset('assets/images/shapes/packages-bg-1-1.png') }});"></div>
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">Pricing Plans</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">Choose the right <br> plan for you</h2>
        </div>

        <div class="packages-one__nav" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            <ul class="nav nav-pills packages-one__nav__pills" id="packagesTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly" type="button" role="tab">Monthly</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="yearly-tab" data-bs-toggle="pill" data-bs-target="#yearly" type="button" role="tab">Yearly</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="packagesTabContent">
            @php
                $monthlyPackages = $packages->filter(fn ($package) => blank($package->billing_period)
                    || str_contains(strtolower($package->billing_period), 'month'));
                $yearlyPackages = $packages->filter(fn ($package) => str_contains(strtolower($package->billing_period), 'year'));

                // Agencies that price per project rather than per period still get
                // a populated first tab.
                if ($monthlyPackages->isEmpty() && $yearlyPackages->isEmpty()) {
                    $monthlyPackages = $packages;
                }
            @endphp

            <div class="tab-pane fade show active" id="monthly" role="tabpanel">
                <div class="row gutter-y-30">
                    @forelse ($monthlyPackages->values() as $index => $package)
                        @include('frontend.partials.package-card', ['package' => $package, 'index' => $index])
                    @empty
                        <div class="col-12"><p>No packages have been published yet.</p></div>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="yearly" role="tabpanel">
                <div class="row gutter-y-30">
                    @forelse ($yearlyPackages->values() as $index => $package)
                        @include('frontend.partials.package-card', ['package' => $package, 'index' => $index])
                    @empty
                        <div class="col-12"><p>No yearly packages have been published yet.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
