@extends('layouts.app')

@section('title', '404 Error - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">404 Error</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>404 Error</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="error-404 section-space">
    <div class="container">
        <div class="error-404__content">
            <img loading="lazy" src="{{ asset('assets/images/shapes/error-404.png') }}" alt="error 404" class="error-404__image">
            <h1 class="error-404__title-top" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">404</h1>
            <h2 class="error-404__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Oops! Page Not Found</h2>
            <p class="error-404__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">Sorry This Page Not found take a look at our most popular.</p>
            <form action="{{ route('search') }}" method="GET" role="search" class="error-404__search" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <input name="q" aria-label="Search the site" maxlength="200" type="text" id="error-search" placeholder="Search Here" />
            </form>
            <div class="error-404__btn" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <a href="{{ route('home') }}" class="misiom-btn"><span>Go to Home</span></a>
            </div>
        </div>
    </div>
</section>

@endsection
