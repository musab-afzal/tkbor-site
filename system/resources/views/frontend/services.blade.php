@extends('layouts.app')

@section('title', 'Our Services - '.settings('agency_name', config('app.name')))
@section('meta_description', (string) (settings('services_intro') ?: settings('meta_description')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Our Services</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><span>Our Services</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="services-two section-space services-page">
    <div class="container">
        <div class="services-two__top">
            <div class="sec-title sec-title--center">
                <div class="sec-title__top hidden">
                    <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                        <p class="sec-title__tagline">Our Services</p>
                    </div>
                </div>
                <h2 class="sec-title__title bw-split-text">Offering strategic <br> services for startups.</h2>
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
            @forelse ($services as $index => $service)
                <div class="service-card-two {{ $index === 1 ? 'active' : '' }}">
                    <div class="service-card-two__content">
                        <span class="service-card-two__number"></span>
                        <img loading="lazy" src="{{ asset('assets/images/services/service-card-object-2-'.(($index % 4) + 1).'.png') }}" alt="{{ $service->title }}" class="service-card-two__object">
                        <h3 class="service-card-two__title"><a href="{{ route('service.show', $service->slug) }}">{{ $service->title }}</a></h3>
                        @if ($service->short_description)
                            <ul class="service-card-two__list list-unstyled">
                                @foreach (array_slice(preg_split('/\r\n|\r|\n|,/', $service->short_description), 0, 5) as $point)
                                    @if (trim($point) !== '')
                                        <li>{{ trim($point) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('service.show', $service->slug) }}" class="misiom-btn-two">
                            <span class="misiom-btn-two__text">Read More</span>
                            <span class="misiom-btn-two__icon-box">
                                <span class="misiom-btn-two__icon"><span><i class="fas fa-long-arrow-alt-right"></i></span></span>
                            </span>
                        </a>
                    </div>
                    <img loading="lazy" src="{{ $service->image ? uploaded_url($service->image) : asset('assets/images/services/service-2-1.jpg') }}" alt="{{ $service->title }}" class="service-card-two__image">
                </div>
            @empty
                <p class="services-two__text">Services will appear here once they are added in the admin panel.</p>
            @endforelse
        </div>
    </div>
</section>

@endsection
