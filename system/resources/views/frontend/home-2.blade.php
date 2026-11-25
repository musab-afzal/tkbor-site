@extends('layouts.app')

@section('title', settings('meta_title', settings('agency_name', config('app.name'))))
@section('meta_description', (string) (settings('meta_description')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="hero-two">
    <div class="hero-two__bg" style="background-image: url({{ asset('assets/images/shapes/hero-bg-2-1.png') }});"></div>
    <div class="container">
        <div class="hero-two__content">
            <h1 class="hero-two__title bw-title-anim">Software <br> &amp; Marketing</h1>
            <div class="hero-two__feature">
                <div class="hero-two__feature__content">
                    <ul class="hero-two__list list-unstyled" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <li><span class="hero-two__list__icon"><i class="fas fa-check"></i></span> Build Websites, Apps &amp; AI Automation</li>
                        <li><span class="hero-two__list__icon"><i class="fas fa-check"></i></span> Apply Proven Marketing Principles</li>
                        <li><span class="hero-two__list__icon"><i class="fas fa-check"></i></span> Grow Your Business With Us</li>
                    </ul>
                    <div class="hidden">
                        <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <a href="{{ route('about') }}" class="misiom-btn"><span>Discover more</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-two__bottom">
                <img fetchpriority="high" src="{{ asset('assets/images/shapes/hero-shape-2-2.png') }}" alt="" class="hero-two__bottom__shape" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <h2 class="hero-two__bottom__title bw-reveal-text-2">Agency</h2>
            </div>
        </div>
    </div>
    <div class="hero-two__shape" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
        <img fetchpriority="high" src="{{ asset('assets/images/shapes/hero-shape-2-1.png') }}" alt="" class="hero-two__shape__img bw-animate-rotate">
    </div>
</section>
<section class="slide-text-two">
    <div class="marquee marquee-left--gsap">
        <div class="marquee__toleft">
                        <h3 class="slide-text-two__title">*Creative Digital Agency.</h3>
                        <h3 class="slide-text-two__title">*Modern Web Solutions.</h3>
                        <h3 class="slide-text-two__title">*Digital Marketing Agency.</h3>
                        <h3 class="slide-text-two__title">*Innovative Design Studio.</h3>
                        <h3 class="slide-text-two__title">*Smart Marketing Agency.</h3>
                        <h3 class="slide-text-two__title">*Digital Growth Experts.</h3>
                        <h3 class="slide-text-two__title">*Creative Brand Builders.</h3>
                        <h3 class="slide-text-two__title">*Next Gen Agency.</h3>
                        <h3 class="slide-text-two__title">*Powerful Web Experiences.</h3>
                        <h3 class="slide-text-two__title">*Startup Growth Partner.</h3>
                        <h3 class="slide-text-two__title">*Creative Strategy Hub.</h3>
                    </div>
    </div>
</section>
<section class="about-two section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-7">
                <div class="about-two__content">
                    <div class="sec-title">
                        <div class="sec-title__top">
                            <div class="sec-title__top__box">
                                <p class="sec-title__tagline">{{ settings('home_about_tagline', 'About Us') }}</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">Company</h2>
                    </div>
                    <div class="about-two__info" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                        <div class="about-two__info__left">
                            <img loading="lazy" src="{{ asset('assets/images/shapes/about-icon.png') }}" alt="" class="about-two__info__icon">
                            <h3 class="about-two__info__title">Helping brands grow through creative digital marketing.</h3>
                        </div>
                        <ul class="about-two__info__list list-unstyled">
                            <li><span class="about-two__info__list__icon"><i class="fas fa-arrow-right"></i></span> Website Growths</li>
                            <li><span class="about-two__info__list__icon"><i class="fas fa-arrow-right"></i></span> Digital Marketing Solutions</li>
                            <li><span class="about-two__info__list__icon"><i class="fas fa-arrow-right"></i></span> Best Design Consultancy</li>
                            <li><span class="about-two__info__list__icon"><i class="fas fa-arrow-right"></i></span> Professional Team Memebers</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="about-two__funfact" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                    <h2 class="about-two__funfact__count count-box"><span class="count-text" data-stop="98" data-speed="1500"></span>%</h2>
                    <p class="about-two__funfact__title">Very satisfied clients around the worldwide and they're repeating.</p>
                </div>
                <div class="about-two__image grid-reveal-anim">
                    <img loading="lazy" src="{{ asset('assets/images/about/about-2-1.jpg') }}" alt="{{ settings('agency_name', config('app.name')) }} - About Our Agency" data-animate="true" class="grid-reveal-anim-img">
                </div>
            </div>
        </div>
    </div>
</section>
<section class="video-area video-section image-parallax">
    <video class="bg-video image-parallax-lr" id="bgVideo" loop muted preload="auto">
        <source src="{{ asset('assets/videos/video-1-1.mp4') }}" type="video/mp4" />
    </video>
    <div class="video-overlay"></div>
    <div class="container">
        <div class="video-area__button">
            <button class="video-btn play-btn" aria-label="Play Video">
                <i class="play-btn-icon"><i class="fas fa-play"></i></i>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</section>
<section class="services-two services-two--dark section-space">
    <div class="services-two__bg" style="background-image: url({{ asset('assets/images/shapes/services-bg-2-1.png') }});"></div>
    <div class="container">
        <div class="services-two__top">
            <div class="sec-title sec-title--light sec-title--center">
                <div class="sec-title__top hidden">
                    <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <p class="sec-title__tagline">{{ settings('services_tagline', 'Our Services') }}</p>
                    </div>
                </div>
                <h2 class="sec-title__title bw-split-text">{{ settings('services_heading', 'Offering strategic services for startups.') }}</h2>
            </div>
            <p class="services-two__text bw-reveal-text">{{ settings('services_intro') }}</p>
            <div class="animate-box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                <span class="animate-box__icon"><i class="fas fa-chevron-down"></i></span>
                <span class="animate-box__icon"><i class="fas fa-chevron-down"></i></span>
                <span class="animate-box__icon"><i class="fas fa-chevron-down"></i></span>
                <span class="animate-box__icon"><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="services-two__button hidden">
                <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <a href="{{ route('services') }}" class="misiom-btn"><span>View all services</span></a>
                </div>
            </div>
        </div>
        <div class="services-two__wrapper" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
            @include('frontend.partials.home-service-cards')
                    </div>
        <div class="services-two__mobile-more">
            <a href="{{ route('services') }}" class="misiom-btn"><span>View All Services</span></a>
        </div>
    </div>
</section>
<section class="portfolio-two section-space">
    <div class="container">
        <div class="portfolio-two__top">
            <div class="row gutter-y-30 align-items-center">
                <div class="col-xxl-8 col-xl-7">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">{{ settings('portfolio_tagline', 'Case Studies') }}</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">{{ settings('portfolio_heading', 'Discover case studies and creations.') }}</h2>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-5" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <div class="portfolio-two__right">
                        <p class="portfolio-two__text">Strengths that accelerate brand growth by combining strategic thinking, creative execution.</p>
                        <a href="{{ route('portfolio') }}" class="misiom-btn"><span>View all works</span></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bw-portfolio-wrap">
            @include('frontend.partials.portfolio-cards')
                    </div>
    </div>
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
        <div class="row gutter-y-30">
            @forelse ($packages as $index => $package)
                @include('frontend.partials.package-card', ['package' => $package, 'index' => $index])
            @empty
                <div class="col-12"><p class="portfolio-two__text">Pricing plans will appear here once they are added in the admin panel.</p></div>
            @endforelse
        </div>
        @if ($packages->isNotEmpty())
            <div class="text-center" style="margin-top:40px;">
                <a href="{{ route('packages') }}" class="misiom-btn"><span>View All Plans</span></a>
            </div>
        @endif
    </div>
</section>
<section class="team-two section-space">
    <div class="team-two__bg" style="background-image: url({{ asset('assets/images/backgrounds/team-bg-2-1.jpg') }});"></div>
    <div class="container">
        <div class="row gutter-y-50">
            <div class="col-xl-4">
                <div class="team-two__content">
                    <div class="sec-title sec-title--light">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">{{ settings('team_tagline', 'Team Members') }}</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">{{ settings('team_heading', 'Meet professional behind startups success.') }}</h2>
                    </div>
                    <p class="team-two__text bw-reveal-text">{{ settings('team_intro') }}</p>
                    
                </div>
            </div>
            <div class="col-xl-8">
                <div class="team-two__carousel misiom-owl__carousel misiom-owl__carousel--basic-nav owl-carousel" data-owl-options='{"items":1,"margin":30,"loop":false,"smartSpeed":700,"nav":false,"dots":false,"autoplay":true,"navText":["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"],"responsive":{"0":{"items":1,"nav":true},"768":{"items":2,"nav":true},"992":{"items":3},"1300":{"items":2.3},"1440":{"items":2.5},"1600":{"items":3}}}'>
                    @forelse ($teamMembers as $index => $member)
                        <div class="item" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
                            @include('frontend.partials.team-card', ['member' => $member, 'index' => $index])
                        </div>
                    @empty
                        <div class="item">
                            <p class="team-two__text">Team members will appear here once they are added in the admin panel.</p>
                        </div>
                    @endforelse
                                                        </div>
            </div>
        </div>
    </div>
</section>
@include('frontend.partials.funfacts')
<section class="testimonials-two section-space-t">
    <div class="container">
        <div class="testimonials-two__wrapper">
            <div class="row gutter-y-50">
                <div class="col-xxl-5">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">Testimonials</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">What our clients say about our work</h2>
                    </div>
                </div>
                <div class="col-xxl-7" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                    <div class="testimonials-two__carousel misiom-owl__carousel misiom-owl__carousel--basic-nav owl-carousel" data-owl-options='{"items":1,"margin":30,"loop":false,"smartSpeed":700,"nav":true,"dots":false,"autoplay":true,"navText":["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"]}'>
                        @include('frontend.partials.testimonial-cards')
                                            </div>
                </div>
            </div>
            <img loading="lazy" src="{{ asset('assets/images/shapes/testimonials-shape-2-1.png') }}" alt="" class="testimonials-two__shape bw-animate-rotate">
        </div>
    </div>
</section>
<section class="work-process-two section-space">
    <div class="container">
        <div class="row gutter-y-40">
            <div class="col-xl-6 order-1 order-xl-0">
                <div class="work-process-two__image grid-reveal-anim">
                    <img loading="lazy" src="{{ asset('assets/images/resources/work-process-2-1.jpg') }}" alt="{{ settings('agency_name', config('app.name')) }} - Work Process" data-animate="true" class="grid-reveal-anim-img">
                </div>
            </div>
            <div class="col-xl-6 order-0 order-xl-1">
                <div class="work-process-two__content">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">{{ settings('process_tagline', 'Work Process') }}</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">{{ settings('process_heading', 'Proven method for high-impact products') }}</h2>
                    </div>
                    <div class="work-process-two__wrapper">
                        @include('frontend.partials.work-process-items')
                                            </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="customer-brand customer-brand--three section-space-t">
    <div class="container">
        @include('frontend.partials.brand-grid')
    </div>
</section>
<section class="blog-two blog-two--section section-space">
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">{{ settings('blog_tagline', 'Our Blog') }}</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">Learn from the latest <br> news &amp; daily blogs.</h2>
        </div>
        <div class="row gutter-y-30">
            @include('frontend.partials.home-blog-cards')
                    </div>
    </div>
</section>

@endsection
