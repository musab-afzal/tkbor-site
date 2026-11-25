@extends('layouts.app')

@section('title', $service->title.' | '.settings('agency_name', 'TKBOR'))
@section('meta_image', $service->image ? uploaded_url($service->image) : '')
@section('meta_description', (string) ($service->short_description ?: settings('meta_description')))

@section('content')

    <div id="smooth-wrapper">
        <div id="smooth-content">

            <section class="page-header image-parallax">
                <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
                <div class="container">
                    <div class="page-header__content hidden">
                        <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $service->title }}</h1>
                        <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('services') }}"><span>our services</span></a></li>
                            <li><span>{{ $service->title }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
            </section>

            <section class="service-details section-space">
                <div class="container">
                    <div class="row gutter-y-40">
                        <div class="col-xl-4 col-lg-5">
                            <div class="service-sidebar">
                                <div class="service-sidebar__single" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                                    <ul class="service-sidebar__nav list-unstyled">
                                        <li><a href="{{ route('service.show', $service->slug) }}">{{ $service->title }}<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
                                        @foreach ($otherServices as $other)
                                            <li><a href="{{ route('service.show', $other->slug) }}">{{ $other->title }}<span class="service-sidebar__nav__icon"><i class="fas fa-long-arrow-alt-right"></i></span></a></li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="service-sidebar__single" data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                                    <div class="service-sidebar__contact">
                                        <h2 class="service-sidebar__contact__title">Are you <br> looking <br> for a digital <br> marketing?</h2>
                                        <a href="{{ route('contact') }}" class="misiom-btn"><span>Contact</span></a>
                                        <img loading="lazy" src="{{ asset('assets/images/services/service-sidebar-1.png') }}" alt="service sidebar contact" class="service-sidebar__contact__image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-7">
                            <div class="service-details__content">
                                <div class="service-details__image" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                    <img loading="lazy" src="{{ $service->image ? uploaded_url($service->image) : asset('assets/images/services/service-2-1.jpg') }}" alt="{{ $service->title }}">
                                </div>

                                <h2 class="service-details__title">{{ $service->title }}</h2>

                                @if ($service->short_description)
                                    <p class="service-details__text">{{ $service->short_description }}</p>
                                @endif

                                @if ($service->description)
                                    <div class="service-details__text" style="white-space:pre-line">{{ $service->description }}</div>
                                @endif

                                @if (is_array($service->benefits) && $service->benefits || is_array($service->features) && $service->features)
                                    <div class="row gutter-y-30" style="margin-top:10px;">
                                        @if (is_array($service->benefits) && $service->benefits)
                                            <div class="col-md-6">
                                                <h3 class="service-details__title" style="font-size:22px;margin-bottom:16px;">Key Benefits</h3>
                                                <ul class="about-three__info__list list-unstyled">
                                                    @foreach ($service->benefits as $benefit)
                                                        <li>
                                                            <span class="about-three__info__list__icon"><i class="fas fa-check"></i></span>
                                                            {{ $benefit }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if (is_array($service->features) && $service->features)
                                            <div class="col-md-6">
                                                <h3 class="service-details__title" style="font-size:22px;margin-bottom:16px;">Main Features</h3>
                                                <ul class="about-three__info__list list-unstyled">
                                                    @foreach ($service->features as $feature)
                                                        <li>
                                                            <span class="about-three__info__list__icon"><i class="fas fa-check"></i></span>
                                                            {{ $feature }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div style="margin-top:30px;">
                                    <a href="{{ $service->cta_url ?: route('contact') }}" class="misiom-btn"><span>{{ $service->cta_label ?: 'Get a Free Quote' }}</span></a>
                                </div>

                                @if ($packages->isNotEmpty())
                                    <h3 class="service-details__title" style="margin-top:40px;">Packages</h3>
                                    <div class="row gutter-y-30">
                                        @foreach ($packages as $package)
                                            <div class="col-md-6">
                                                <div class="pricing-card">
                                                    <div class="pricing-card__top">
                                                        <h3 class="pricing-card__title">{{ $package->name }}</h3>
                                                        @if ($package->is_recommended)
                                                            <span class="pricing-card__badge">Recommended</span>
                                                        @endif
                                                    </div>
                                                    <h2 class="pricing-card__price">
                                                        {{ money($package->effectivePrice(), $package->currency) }}
                                                        @if ($package->billing_period)<span>/{{ $package->billing_period }}</span>@endif
                                                    </h2>
                                                    @if ($package->short_description)
                                                        <p class="pricing-card__text">{{ $package->short_description }}</p>
                                                    @endif
                                                    @if (is_array($package->features) && $package->features)
                                                        <ul class="pricing-card__list list-unstyled">
                                                            @foreach ($package->features as $feature)
                                                                <li><i class="fas fa-check"></i> {{ $feature }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    <a href="{{ route('contact') }}" class="misiom-btn"><span>Get Started</span></a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($faqs->isNotEmpty())
                                    <h3 class="service-details__title" style="margin-top:40px;">Frequently Asked Questions</h3>
                                    <div class="misiom-accrodion" data-grp-name="faq-accrodion">
                                        @foreach ($faqs as $index => $faq)
                                            <div class="accrodion {{ $index === 0 ? 'active' : '' }}">
                                                <div class="accrodion-title">
                                                    <h4>{{ $faq->question }}<span class="accrodion-title__icon"></span></h4>
                                                </div>
                                                <div class="accrodion-content">
                                                    <div class="inner">
                                                        <p>{{ $faq->answer }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

@endsection

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => $service->title,
    'description' => $service->short_description ?: $service->description,
    'url' => route('service.show', $service->slug),
    'provider' => [
        '@type' => 'Organization',
        'name' => settings('agency_name', config('app.name')),
        'url' => url('/'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@include('frontend.partials.breadcrumb-schema', ['items' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Services', 'url' => route('services')],
    ['label' => $service->title, 'url' => route('service.show', $service->slug)],
]])
@endpush
