@extends('layouts.app')

@section('title', 'About Us - '.settings('agency_name', config('app.name')))
@section('meta_description', (string) (settings('about_intro') ?: settings('meta_description')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">About Us</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>About Us</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="about-three section-space">
    <div class="container">
        <div class="row gutter-y-30">
            <div class="col-lg-7">
                <div class="about-three__content">
                    <div class="sec-title sec-title--row hidden">
                        <div class="sec-title__top">
                            <div class="sec-title__top__box">
                                <p class="sec-title__tagline">{{ settings('about_tagline', 'About Company') }}</p>
                            </div>
                        </div>
                        <div class="sec-title__bottom">
                            <h2 class="sec-title__title bw-split-text">{{ settings('about_heading', "We're creating digital experience that amplify your brand.") }}</h2>
                            <p class="about-three__text bw-reveal-text">{{ settings('about_intro') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="about-three__funfact">
                    <h2 class="about-three__funfact__count count-box">
                        <span class="count-text" data-stop="{{ (int) settings('about_experience_value', 13) }}" data-speed="1500">0</span>+
                    </h2>
                    <p class="about-three__funfact__title">{{ settings('about_experience_label', 'Years of experience in marketing') }}</p>
                </div>
            </div>
        </div>
        <div class="about-three__bottom">
            <div class="row gutter-y-30">
                <div class="col-xl-7">
                    <div class="about-three__image grid-reveal-anim">
                        <img loading="lazy" src="{{ settings('about_image') ? uploaded_url(settings('about_image')) : asset('assets/images/about/about-3-1.jpg') }}"
                             alt="{{ settings('agency_name', 'About') }}" data-animate="true" class="grid-reveal-anim-img">
                    </div>
                </div>
                <div class="col-xl-5" data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                    <div class="about-three__info">
                        <div class="about-three__info__box">
                            <div class="about-three__info__icon">
                                <img loading="lazy" src="{{ asset('assets/images/shapes/about-icon.png') }}" alt="">
                            </div>
                            <div class="about-three__info__content">
                                <h3 class="about-three__info__title">{{ settings('about_info_title', 'Helping brands grow through creative digital marketing.') }}</h3>
                                <ul class="about-three__info__list list-unstyled">
                                    @foreach ($highlights as $highlight)
                                        <li>
                                            <span class="about-three__info__list__icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                                            {{ $highlight->title }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <img loading="lazy" src="{{ asset('assets/images/shapes/about-shape-3-1.png') }}" alt="" class="about-three__shape right-to-left-anim">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="video-area video-section image-parallax">
    <video class="bg-video image-parallax-lr" id="bgVideo" loop muted preload="auto">
        <source src="{{ settings('about_video_url') ?: asset('assets/videos/video-1-1.mp4') }}" type="video/mp4" />
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

@if ($teamMembers->isNotEmpty())
<section class="team-one section-space">
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">{{ settings('team_tagline', 'Team Members') }}</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">{{ settings('team_heading', 'Meet professional behind startups success.') }}</h2>
        </div>
        <div class="team-one__carousel misiom-owl__carousel misiom-owl__carousel--basic-nav owl-carousel" data-owl-options='{"items":1,"margin":30,"loop":false,"smartSpeed":700,"nav":false,"dots":false,"autoplay":true,"navText":["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"],"responsive":{"0":{"items":1,"nav":true,"dots":false},"768":{"items":2},"992":{"items":3}}}'>
            @foreach ($teamMembers as $index => $member)
                <div class="item" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="{{ (($index % 3) + 1) * 100 }}">
                    @include('frontend.partials.team-card', ['member' => $member, 'index' => $index])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="why-choose section-space">
    <div class="why-choose__image grid-reveal-anim">
        <img loading="lazy" src="{{ settings('why_choose_image') ? uploaded_url(settings('why_choose_image')) : asset('assets/images/resources/why-choose-1-1.jpg') }}"
             alt="{{ settings('why_choose_heading', 'Why Choose Us') }}" data-animate="true" class="grid-reveal-anim-img">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-5 col-lg-6 ms-auto">
                <div class="sec-title sec-title--light">
                    <div class="sec-title__top hidden">
                        <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            <p class="sec-title__tagline">{{ settings('why_choose_tagline', 'Why Choose Us') }}</p>
                        </div>
                    </div>
                    <h2 class="sec-title__title bw-split-text">{{ settings('why_choose_heading', 'Why Choose us?') }}</h2>
                </div>
            </div>
        </div>
    </div>
    @if ($whyChoose->isNotEmpty())
        <div class="container why-choose__container">
            <div class="why-choose__wrapper">
                <div class="row gutter-y-30">
                    @include('frontend.partials.why-choose-items')
                </div>
            </div>
        </div>
    @endif
    @include('frontend.partials.marquee')
</section>

@include('frontend.partials.funfacts')

<section class="testimonials-one section-space">
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">{{ settings('testimonials_tagline', 'Testimonials') }}</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">{{ settings('testimonials_heading', 'Loved by thousands of happy clients') }}</h2>
        </div>
        @include('frontend.partials.testimonial-grid')
    </div>
</section>

<section class="customer-brand customer-brand--three section-space-b">
    <div class="container">
        <div class="sec-title sec-title--center">
            <div class="sec-title__top hidden">
                <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                    <p class="sec-title__tagline">{{ settings('brands_tagline', 'Global Clients') }}</p>
                </div>
            </div>
            <h2 class="sec-title__title bw-split-text">{{ settings('brands_heading', 'Helping 100+ brands success.') }}</h2>
        </div>
        @include('frontend.partials.brand-grid')
    </div>
</section>

@endsection
