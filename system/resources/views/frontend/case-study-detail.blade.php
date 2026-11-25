@extends('layouts.app')

@section('title', str_contains(($caseStudy->meta_title ?: $caseStudy->title), settings('agency_name', 'TKBOR')) ? ($caseStudy->meta_title ?: $caseStudy->title) : ($caseStudy->meta_title ?: $caseStudy->title).' | '.settings('agency_name', 'TKBOR'))
@section('meta_image', $caseStudy->featured_image ? uploaded_url($caseStudy->featured_image) : '')
@section('meta_description', (string) ($caseStudy->meta_description ?: $caseStudy->short_description))

@section('content')

    <div id="smooth-wrapper">
        <div id="smooth-content">

            <section class="page-header image-parallax">
                <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
                <div class="container">
                    <div class="page-header__content hidden">
                        <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">{{ $caseStudy->title }}</h1>
                        <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('case-studies') }}"><span>Case Studies</span></a></li>
                            <li><span>{{ $caseStudy->title }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
            </section>

            <section class="portfolio-details section-space">
                <div class="container">
                    <div class="portfolio-details__content">
                        <h2 class="portfolio-details__title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{{ $caseStudy->title }}</h2>

                        @if ($caseStudy->featured_image)
                            <div class="portfolio-details__thumbnail">
                                <div data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                    <div class="scale-anim">
                                        <img loading="lazy" src="{{ uploaded_url($caseStudy->featured_image) }}" alt="{{ $caseStudy->title }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <ul class="portfolio-details__info list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                            @if ($caseStudy->client)
                                <li>
                                    <h3 class="portfolio-details__info__title">Client:</h3>
                                    <p class="portfolio-details__info__text">{{ $caseStudy->client->displayName() }}</p>
                                </li>
                            @endif
                            <li>
                                <h3 class="portfolio-details__info__title">Industry:</h3>
                                <p class="portfolio-details__info__text">{{ $caseStudy->industry ?: '—' }}</p>
                            </li>
                            @if (is_array($caseStudy->technologies) && $caseStudy->technologies)
                                <li>
                                    <h3 class="portfolio-details__info__title">Technologies:</h3>
                                    <p class="portfolio-details__info__text">{{ implode(', ', $caseStudy->technologies) }}</p>
                                </li>
                            @endif
                        </ul>

                        @if ($caseStudy->short_description)
                            <div class="portfolio-details__box" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <h3 class="portfolio-details__box__title">{{ $caseStudy->short_description }}</h3>
                            </div>
                        @endif

                        <div class="portfolio-details__challenges">
                            @foreach (['The Challenge' => $caseStudy->challenge, 'Our Solution' => $caseStudy->solution, 'The Results' => $caseStudy->results] as $heading => $body)
                                @if (filled($body))
                                    <h3 class="portfolio-details__sub-title" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">{{ $heading }}</h3>
                                    <div class="portfolio-details__text">{!! $body !!}</div>
                                @endif
                            @endforeach
                        </div>

                        <div style="margin-top:10px;display:flex;gap:16px;flex-wrap:wrap;">
                            @if ($caseStudy->portfolio)
                                <a href="{{ route('portfolio.show', $caseStudy->portfolio->slug) }}" class="misiom-btn"><span>View the project</span></a>
                            @endif
                            <a href="{{ route('contact') }}" class="misiom-btn misiom-btn--black"><span>Discuss Your Project</span></a>
                        </div>
                    </div>
                </div>
            </section>

@endsection

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $caseStudy->title,
    'description' => $caseStudy->short_description,
    'about' => $caseStudy->industry,
    'author' => ['@type' => 'Organization', 'name' => settings('agency_name', config('app.name'))],
    'publisher' => ['@type' => 'Organization', 'name' => settings('agency_name', config('app.name'))],
    'datePublished' => optional($caseStudy->created_at)->toIso8601String(),
    'dateModified' => optional($caseStudy->updated_at)->toIso8601String(),
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@include('frontend.partials.breadcrumb-schema', ['items' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Case Studies', 'url' => route('case-studies')],
    ['label' => $caseStudy->title, 'url' => route('case-study.show', $caseStudy->slug)],
]])
@endpush

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'headline' => $caseStudy->title,
    'description' => strip_tags((string) ($caseStudy->short_description ?: $caseStudy->description)),
    'image' => $caseStudy->featured_image ? uploaded_url($caseStudy->featured_image) : null,
    'creator' => [
        '@type' => 'Organization',
        'name' => settings('agency_name', config('app.name')),
        'url' => url('/'),
    ],
    'mainEntityOfPage' => route('case-study.show', $caseStudy->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush