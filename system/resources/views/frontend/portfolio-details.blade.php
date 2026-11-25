@extends('layouts.app')

@section('title', $portfolio->title.' | '.settings('agency_name', 'TKBOR'))
@section('meta_image', $portfolio->featured_image ? uploaded_url($portfolio->featured_image) : '')

@section('meta_description', (string) ($portfolio->short_description ?: settings('meta_description')))

@section('content')

    <div id="smooth-wrapper">
        <div id="smooth-content">

            <section class="page-header image-parallax">
                <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
                <div class="container">
                    <div class="page-header__content hidden">
                        <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Portfolio Details</h1>
                        <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('portfolio') }}"><span>Our Portfolio</span></a></li>
                            <li><span>{{ $portfolio->title }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
            </section>

            <section class="portfolio-details section-space">
                <div class="container">
                    <div class="portfolio-details__content">
                        {{-- 1. Main Project Image --}}
                        <div class="portfolio-details__thumbnail" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            <div class="scale-anim">
                                <img loading="lazy" src="{{ $portfolio->featured_image ? uploaded_url($portfolio->featured_image) : asset('assets/images/portfolio/portfolio-details-1.jpg') }}" alt="{{ $portfolio->title }}">
                            </div>
                        </div>

                        {{-- 2. Project Information --}}
                        <ul class="portfolio-details__info list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            @if ($portfolio->project_date)
                                <li>
                                    <h3 class="portfolio-details__info__title">Date:</h3>
                                    <p class="portfolio-details__info__text">{{ $portfolio->project_date->format('d F Y') }}</p>
                                </li>
                            @endif
                            @if (filled($portfolio->category))
                                <li>
                                    <h3 class="portfolio-details__info__title">Services:</h3>
                                    <p class="portfolio-details__info__text">{{ $portfolio->category }}</p>
                                </li>
                            @endif
                            @if (filled($portfolio->client_name))
                                <li>
                                    <h3 class="portfolio-details__info__title">Client:</h3>
                                    <p class="portfolio-details__info__text">{{ $portfolio->client_name }}</p>
                                </li>
                            @endif
                            @php
                                $techList = is_array($portfolio->technologies) ? array_filter($portfolio->technologies) : (is_string($portfolio->technologies) && filled($portfolio->technologies) ? [trim($portfolio->technologies)] : []);
                            @endphp
                            @if (!empty($techList))
                                <li>
                                    <h3 class="portfolio-details__info__title">Technologies:</h3>
                                    <p class="portfolio-details__info__text">{{ implode(', ', $techList) }}</p>
                                </li>
                            @endif
                        </ul>

                        {{-- 3. Project Heading / Title --}}
                        <h2 class="portfolio-details__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{{ $portfolio->title }}</h2>

                        {{-- 4. Project Detail / Description Paragraph --}}
                        @if ($portfolio->short_description)
                            <p class="portfolio-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{{ $portfolio->short_description }}</p>
                        @endif

                        @if ($portfolio->description)
                            <div class="portfolio-details__text" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" style="white-space:pre-line">{{ $portfolio->description }}</div>
                        @endif
                    </div>
                </div>
            </section>

@endsection

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'headline' => $portfolio->title,
    'description' => strip_tags((string) ($portfolio->short_description ?: $portfolio->description)),
    'image' => $portfolio->featured_image ? uploaded_url($portfolio->featured_image) : null,
    'creator' => [
        '@type' => 'Organization',
        'name' => settings('agency_name', config('app.name')),
        'url' => url('/'),
    ],
    'mainEntityOfPage' => route('portfolio.show', $portfolio->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@include('frontend.partials.breadcrumb-schema', ['items' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Our Portfolio', 'url' => route('portfolio')],
    ['label' => $portfolio->title, 'url' => route('portfolio.show', $portfolio->slug)],
]])
@endpush
