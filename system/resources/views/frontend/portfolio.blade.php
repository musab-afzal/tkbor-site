@extends('layouts.app')

@section('title', 'Portfolio - '.settings('agency_name', config('app.name')))
@section('meta_description', 'Explore our portfolio of cutting-edge web development, UI/UX design, and digital solutions delivered for industry-leading clients.')

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Our Portfolio</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Portfolio</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="portfolio-two section-space">
    <div class="container">
        <div class="portfolio-two__top">
            <div class="row gutter-y-30 align-items-center">
                <div class="col-xxl-8 col-xl-7">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">Case Studies</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">Discover case studies <br> and creations.</h2>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-5" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="portfolio-two__right"><p class="portfolio-two__text" style="margin-bottom: 0;">Strengths that accelerate brand growth by combining strategic thinking, creative execution.</p></div>
                </div>
            </div>
        </div>
        <div class="bw-portfolio-wrap">
            @include('frontend.partials.portfolio-cards')
        </div>

        @include('frontend.partials.pagination', ['paginator' => $portfolios])
    </div>
</section>

@endsection
