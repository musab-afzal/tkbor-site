@extends('layouts.app')

@section('title', 'Search - '.settings('agency_name', config('app.name')))

@section('content')

        <div id="smooth-wrapper">
            <div id="smooth-content">

                <section class="page-header image-parallax">
    <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
    <div class="container">
        <div class="page-header__content hidden">
            <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Search</h1>
            <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>Search</span></li>
            </ul>
        </div>
    </div>
    <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
</section>

<section class="blog-page section-space">
    <div class="container">

        <form role="search" method="GET" action="{{ route('search') }}" class="error-404__search" style="margin-bottom: 40px;">
            <input maxlength="200" aria-label="Search the site" type="text" name="q" value="{{ $term }}" placeholder="Search services, work, articles …" required>
        </form>

        @if ($term === '')
            <p class="portfolio-two__text">Type something above to search the site.</p>
        @elseif ($results->isEmpty())
            <h2 class="sec-title__title" style="margin-bottom: 14px;">No matches for &ldquo;{{ $term }}&rdquo;</h2>
            <p class="portfolio-two__text">
                Try a different word, or <a href="{{ route('contact') }}">send us a message</a> and we will point you in the right direction.
            </p>
        @else
            <h2 class="sec-title__title" style="margin-bottom: 26px;">
                {{ $results->count() }} {{ Str::plural('result', $results->count()) }} for &ldquo;{{ $term }}&rdquo;
            </h2>

            <div class="row gutter-y-30">
                @foreach ($results as $result)
                    <div class="col-lg-6">
                        <div class="why-choose__item" style="height: 100%;">
                            <div class="why-choose__item__top">
                                <div class="why-choose__item__icon"><i class="fas fa-arrow-right"></i></div>
                                <h3 class="why-choose__item__title">
                                    <a href="{{ $result['url'] }}">{{ $result['title'] }}</a>
                                </h3>
                            </div>
                            <p class="why-choose__item__text">
                                <strong>{{ $result['type'] }}</strong>
                                @if (! empty($result['excerpt'])) — {{ Str::limit($result['excerpt'], 140) }} @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>

@endsection
