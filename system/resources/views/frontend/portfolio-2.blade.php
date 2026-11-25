@extends('layouts.app')

@section('title', 'Portfolio 2 - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Portfolio Page 02</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Portfolio</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="portfolio-three section-space">
    <div class="container">
        <div class="sec-title">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">Case Studies</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">Discover case studies <br> and creations.</h2>
        </div>
        <div class="row gutter-y-30">
                        <div class="portfolio-three__col portfolio-three__col--1 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-1.jpg') }}" alt="Elevate Corporate Identity">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">Brand Strategy</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Elevate Corporate Identity</a></h3>
                    </div>
                </div>
            </div>
                        <div class="portfolio-three__col portfolio-three__col--2 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-2.jpg') }}" alt="Design Smarter Experiences">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">UX Research</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Design Smarter Experiences</a></h3>
                    </div>
                </div>
            </div>
                        <div class="portfolio-three__col portfolio-three__col--1 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-3.jpg') }}" alt="Ignite Unstoppable Growth">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">Creative Campaign</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Ignite Unstoppable Growth</a></h3>
                    </div>
                </div>
            </div>
                        <div class="portfolio-three__col portfolio-three__col--2 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-4.jpg') }}" alt="Bold Brand Identity">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">Visual Branding</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Bold Brand Identity</a></h3>
                    </div>
                </div>
            </div>
                        <div class="portfolio-three__col portfolio-three__col--1 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-5.jpg') }}" alt="Creative Corporate Mockup">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">Sign Design</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Creative Corporate Mockup</a></h3>
                    </div>
                </div>
            </div>
                        <div class="portfolio-three__col portfolio-three__col--2 bw-item-flip-in">
                <div class="portfolio-three__card bw-hover-item">
                    <a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}" class="portfolio-three__card__image bw-hover-img" data-displacement="https://pixydrops.com/misiom-laravel/assets/images/image-hover/stripe-mul.png" data-intensity="0.4" data-speedin="1" data-speedout="1">
                        <img loading="lazy" src="{{ asset('assets/images/portfolio/portfolio-3-6.jpg') }}" alt="Drive Business Growth">
                    </a>
                    <div class="portfolio-three__card__content">
                        <p class="portfolio-three__card__tagline">Content Planning</p>
                        <h3 class="portfolio-three__card__title"><a href="{{ route('portfolio.show', $portfolio->slug ?? '') }}">Drive Business Growth</a></h3>
                    </div>
                </div>
            </div>
                    </div>
    </div>
</section>

@endsection
