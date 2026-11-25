@extends('layouts.app')

@section('title', 'Careers - '.$site->get('site_title', config('app.name')))

@section('content')

    <div id="smooth-wrapper">
        <div id="smooth-content">

            <section class="page-header image-parallax">
                <div class="page-header__bg image-parallax-lr" style="background-image: url({{ asset('assets/images/backgrounds/page-header-bg.jpg') }});"></div>
                <div class="container">
                    <div class="page-header__content hidden">
                        <h1 class="page-header__title" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">Careers</h1>
                        <ul class="misiom-breadcrumb list-unstyled" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300" data-aos-delay="100">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><span>Join our team</span></li>
                        </ul>
                    </div>
                </div>
                <div class="page-header__shape" style="background-image: url({{ asset('assets/images/shapes/page-header-shape.png') }});"></div>
            </section>

            <section class="team-one section-space">
                <div class="container">
                    <div class="sec-title">
                        <div class="sec-title__top hidden">
                            <div class="sec-title__top__box" data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <p class="sec-title__tagline">Open Positions</p>
                            </div>
                        </div>
                        <h2 class="sec-title__title bw-split-text">Build your career <br> with our team.</h2>
                    </div>

                    <div class="row gutter-y-30">
                        @forelse ($careers as $career)
                            <div class="col-lg-6" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1300">
                                <div class="faq-one__faq__item">
                                    <div class="faq-one__faq__content">
                                        <h3 class="faq-one__faq__title">{{ $career->title }}</h3>

                                        <p class="faq-one__faq__text">
                                            @if ($career->department){{ $career->department->name }} &middot; @endif
                                            {{ \App\Models\Employee::EMPLOYMENT_TYPES[$career->employment_type] ?? $career->employment_type }}
                                            @if ($career->location) &middot; {{ $career->location }}@endif
                                            @if ($career->experience_level) &middot; {{ \App\Models\Career::EXPERIENCE_LEVELS[$career->experience_level] ?? $career->experience_level }}@endif
                                        </p>

                                        @if ($career->description)
                                            <p class="faq-one__faq__text" style="white-space:pre-line">{{ $career->description }}</p>
                                        @endif

                                        @if ($career->requirements)
                                            <p class="faq-one__faq__text"><strong>Requirements:</strong></p>
                                            <p class="faq-one__faq__text" style="white-space:pre-line">{{ $career->requirements }}</p>
                                        @endif

                                        @if ($career->salary_range)
                                            <p class="faq-one__faq__text"><strong>Salary:</strong> {{ $career->salary_range }}</p>
                                        @endif

                                        @if ($career->closing_date)
                                            <p class="faq-one__faq__text">Applications close {{ $career->closing_date->format('F d, Y') }}.</p>
                                        @endif

                                        <a href="{{ route('contact') }}" class="misiom-btn"><span>Apply now</span></a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-lg-12">
                                <p>There are no open positions right now — check back soon.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

@endsection
